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
 * Enrolling engineers and leaders.
 *
 * The director sets a first password and the engineer changes it; there is no
 * mail-based invitation flow, because on this host outbound mail goes through
 * the local relay and a lost invitation is a support call rather than a
 * self-service reset.
 */
class PeopleController extends Controller
{
    public function index(): View
    {
        return view('erp.people', [
            'people' => User::query()
                ->whereNotNull('erp_role')
                ->withCount(['assignedTasks as open_tasks_count' => fn ($q) => $q->open()])
                ->orderBy('name')
                ->get(),
            'roles' => ErpRole::cases(),
            'siteOnly' => User::query()->whereNull('erp_role')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'erp_role' => ['required', Rule::enum(ErpRole::class)],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()->symbols()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'job_title' => $data['job_title'] ?? null,
            'erp_role' => $data['erp_role'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'is_admin' => false,
        ]);

        return back()->with('status', $user->name.' can now sign in. Give them the password directly and have them change it.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'erp_role' => ['required', Rule::enum(ErpRole::class)],
            'job_title' => ['nullable', 'string', 'max:120'],
            'is_active' => ['required', 'boolean'],
        ]);

        // The last active director must not be able to demote or suspend
        // themselves: nobody would be left who can open a project or enrol
        // anyone, and there is no way back in through the interface.
        $isLastDirector = $user->erpRole() === ErpRole::Director
            && User::query()->where('erp_role', ErpRole::Director->value)
                ->where('is_active', true)->count() === 1;

        if ($isLastDirector && ($data['erp_role'] !== ErpRole::Director->value || ! $data['is_active'])) {
            return back()->withErrors([
                'erp_role' => 'This is the only active delivery director. Appoint another one first, or nobody will be able to open a project.',
            ]);
        }

        $user->update($data);

        return back()->with('status', 'Updated '.$user->name.'.');
    }
}
