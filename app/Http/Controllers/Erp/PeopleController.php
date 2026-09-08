<?php

namespace App\Http\Controllers\Erp;

use App\Erp\Enums\ErpRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Enrolling people onto the posts published at /company/leadership.
 *
 * Leadership creates accounts, not only the directors — but each post may only
 * create the posts beneath it. A practice lead enrols engineers; without that
 * limit, the ability to create an account would be a route to granting yourself
 * or a colleague authority that nobody appointed.
 *
 * The enroller sets a first password and hands it over directly. There is no
 * emailed invitation: on this host outbound mail goes through the local relay,
 * and an invitation that silently fails to arrive is a support call rather than
 * a self-service reset.
 */
class PeopleController extends Controller
{
    public function index(Request $request): View
    {
        $actor = $request->user();

        $people = User::query()
            ->whereNotNull('erp_role')
            ->withCount(['assignedTasks as open_tasks_count' => fn ($q) => $q->open()])
            ->orderBy('name')
            ->get();

        return view('erp.people', [
            'actor' => $actor,
            'leadership' => $people->filter(fn (User $u) => $u->erpRole()?->isLeadership()),
            'engineers' => $people->reject(fn (User $u) => $u->erpRole()?->isLeadership()),
            'creatable' => $actor->erpRole()->mayCreate(),
            'mayChangeRoles' => $actor->erpRole()->opensProjects(),
            'disciplines' => ErpRole::practiceDisciplines(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $actor = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'erp_role' => ['required', Rule::enum(ErpRole::class)],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()->symbols()],
        ]);

        $role = ErpRole::from($data['erp_role']);

        if (! $actor->erpRole()->mayCreateRole($role)) {
            return back()->withErrors([
                'erp_role' => sprintf(
                    'As %s you may enrol %s. Appointing a %s is for the Managing Director, the Chief Technology Officer or the Director of Delivery.',
                    $actor->erpRole()->label(),
                    collect($actor->erpRole()->mayCreate())->map->label()->join(', ', ' or '),
                    $role->label(),
                ),
            ])->withInput();
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'job_title' => ($data['job_title'] ?? '') ?: $role->label(),
            'erp_role' => $role,
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'is_admin' => false,
        ]);

        return back()->with('status', sprintf(
            '%s enrolled as %s. Give them the password directly and have them change it.',
            $user->name, $role->label(),
        ));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();

        // Changing what post somebody holds is a different act from enrolling
        // an engineer, and stays with the directors.
        abort_unless($actor->erpRole()->opensProjects(), 403,
            'Changing a post is reserved to the Managing Director, the Chief Technology Officer and the Director of Delivery.');

        $data = $request->validate([
            'erp_role' => ['required', Rule::enum(ErpRole::class)],
            'job_title' => ['nullable', 'string', 'max:120'],
            'is_active' => ['required', 'boolean'],
        ]);

        $role = ErpRole::from($data['erp_role']);

        // Somebody has to be left who can open an engagement. Without this the
        // last director can demote themselves and lock the whole division out,
        // with no way back in through the interface.
        if ($this->wouldLeaveNobodyInCharge($user, $role, (bool) $data['is_active'])) {
            return back()->withErrors([
                'erp_role' => 'That would leave nobody able to open an engagement. Appoint another Managing Director, Chief Technology Officer or Director of Delivery first.',
            ]);
        }

        $user->update([
            'erp_role' => $role,
            'job_title' => ($data['job_title'] ?? '') ?: $role->label(),
            'is_active' => (bool) $data['is_active'],
        ]);

        return back()->with('status', 'Updated '.$user->name.'.');
    }

    private function wouldLeaveNobodyInCharge(User $user, ErpRole $role, bool $active): bool
    {
        if (! $user->erpRole()?->opensProjects()) {
            return false;
        }

        if ($role->opensProjects() && $active) {
            return false;
        }

        $others = User::query()
            ->whereKeyNot($user->id)
            ->where('is_active', true)
            ->whereIn('erp_role', collect(ErpRole::cases())
                ->filter->opensProjects()
                ->map->value
                ->all())
            ->count();

        return $others === 0;
    }
}
