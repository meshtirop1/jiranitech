<?php

namespace App\Http\Controllers\Erp;

use App\Erp\Enums\ProjectRole;
use App\Erp\Enums\ProjectStatus;
use App\Erp\Enums\TaskStatus;
use App\Erp\Models\ActivityEntry;
use App\Erp\Models\Project;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        return view('erp.projects.index', [
            'projects' => Project::query()
                ->visibleTo($request->user())
                ->with(['tasks', 'members'])
                ->orderByRaw("CASE status WHEN 'active' THEN 1 WHEN 'discovery' THEN 2 WHEN 'on_hold' THEN 3 ELSE 4 END")
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(Request $request, Project $project): View
    {
        $user = $request->user();
        abort_unless($project->mayBeSeenBy($user), 403,
            'You are not on this project. Ask the project lead or a director to add you.');

        $project->load(['members', 'tasks.assignee']);

        // Grouped by state, in the order a lead reads a board: what is stuck,
        // what is waiting on them, then what is moving.
        $board = collect(TaskStatus::boardOrder())
            ->mapWithKeys(fn (TaskStatus $s) => [
                $s->value => $project->tasks->filter(fn ($t) => $t->status === $s)->values(),
            ])
            ->filter(fn ($tasks) => $tasks->isNotEmpty());

        return view('erp.projects.show', [
            'project' => $project,
            'progress' => $project->progress(),
            'board' => $board,
            'mayDirect' => $project->mayBeDirectedBy($user),
            'mayReview' => $project->mayBeReviewedBy($user),
            // Both gates apply: the project role must permit work, and so must
            // the post. The Data Protection Officer passes the first and fails
            // the second, which is the statutory independence the leadership
            // page claims.
            'assignable' => $project->members->filter(
                fn (User $u) => ProjectRole::from($u->pivot->role)->mayBeAssignedWork()
                    && (bool) $u->erpRole()?->mayHoldWork()
            ),
            'candidates' => User::query()->inDelivery()
                ->whereNotIn('id', $project->members->modelKeys())
                ->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('erp.projects.create', [
            'people' => User::query()->inDelivery()->orderBy('name')->get(),
            'statuses' => ProjectStatus::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'client' => ['nullable', 'string', 'max:150'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'repository_url' => ['nullable', 'url', 'max:400'],
            'default_branch' => ['required', 'string', 'max:80'],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'started_on' => ['nullable', 'date'],
            'target_date' => ['nullable', 'date', 'after_or_equal:started_on'],
            'lead_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $project = Project::create([
            ...collect($data)->except('lead_id')->all(),
            'code' => $this->nextCode(),
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(4)),
        ]);

        // A project without a lead has nobody who can accept work, so the lead is
        // required at creation rather than added afterwards.
        $project->members()->attach($data['lead_id'], ['role' => ProjectRole::Lead->value]);

        ActivityEntry::record($project, 'created', $request->user());

        return redirect()->route('erp.projects.show', $project)
            ->with('status', 'Project '.$project->code.' opened.');
    }

    public function addMember(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->mayBeDirectedBy($request->user()), 403);

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role' => ['required', Rule::enum(ProjectRole::class)],
        ]);

        $project->members()->syncWithoutDetaching([
            $data['user_id'] => ['role' => $data['role']],
        ]);

        ActivityEntry::record($project, 'enrolled', $request->user(), null, null,
            User::find($data['user_id'])?->name);

        return back()->with('status', 'Added to the project.');
    }

    private function nextCode(): string
    {
        $n = Project::query()->count() + 1;

        return sprintf('%s-%03d', config('erp.project_prefix'), $n);
    }
}
