<?php

namespace App\Http\Controllers\Erp;

use App\Erp\Enums\TaskStatus;
use App\Erp\Models\ActivityEntry;
use App\Erp\Models\Project;
use App\Erp\Models\Task;
use App\Erp\Support\TaskTransition;
use App\Erp\Support\TaskWorkflow;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class TaskController extends Controller
{
    public function show(Request $request, Task $task): View
    {
        $user = $request->user();
        abort_unless($task->project->mayBeSeenBy($user), 403);

        $task->load(['project.members', 'assignee', 'author', 'reviews.reviewer', 'updates.user']);

        return view('erp.tasks.show', [
            'task' => $task,
            'available' => TaskWorkflow::availableTo($task, $user),
            'blockedBy' => TaskWorkflow::blockingReason($task, $user),
            'activity' => ActivityEntry::query()
                ->where('subject_type', Task::class)
                ->where('subject_id', $task->id)
                ->with('user')
                ->latest('created_at')
                ->get(),
        ]);
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->mayBeDirectedBy($request->user()), 403,
            'Only the project lead can add work to this project.');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'weight' => ['required', 'integer', 'min:1', 'max:21'],
            'due_on' => ['nullable', 'date'],
        ]);

        $task = $project->tasks()->create([
            ...$data,
            'reference' => $this->nextReference($project),
            'created_by' => $request->user()->id,
            'status' => TaskStatus::Backlog,
        ]);

        ActivityEntry::record($task, 'created', $request->user());

        return redirect()->route('erp.tasks.show', $task)
            ->with('status', 'Task '.$task->reference.' created.');
    }

    /**
     * Editing the task's own fields — including the documentation, which is what
     * the workflow requires before review.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $user = $request->user();
        $isAssignee = $task->assignee_id === $user->id;

        abort_unless($isAssignee || $task->project->mayBeDirectedBy($user), 403,
            'Only the assignee or the project lead can edit this task.');

        $rules = [
            'documentation' => ['nullable', 'string', 'max:20000'],
            'branch' => ['nullable', 'string', 'max:160'],
            'pull_request_url' => ['nullable', 'url', 'max:400'],
            'blocked_reason' => ['nullable', 'string', 'max:1000'],
        ];

        // Only whoever directs the project may move work between people or
        // change what it is; the assignee documents and links it.
        if ($task->project->mayBeDirectedBy($user)) {
            $rules += [
                'title' => ['required', 'string', 'max:200'],
                'description' => ['nullable', 'string', 'max:5000'],
                'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
                'weight' => ['required', 'integer', 'min:1', 'max:21'],
                'due_on' => ['nullable', 'date'],
            ];
        }

        $data = $request->validate($rules);
        $documentationChanged = array_key_exists('documentation', $data)
            && $data['documentation'] !== $task->documentation;
        $assigneeChanged = array_key_exists('assignee_id', $data)
            && (int) $data['assignee_id'] !== (int) $task->assignee_id;

        $task->update($data);

        if ($documentationChanged) {
            ActivityEntry::record($task, 'documented', $user);
        }

        if ($assigneeChanged) {
            ActivityEntry::record($task, 'assigned', $user, null, null,
                $task->assignee?->name ?? 'Unassigned');
        }

        return back()->with('status', 'Saved.');
    }

    /**
     * Move the task. Every rule lives in TaskWorkflow; this only reports the
     * refusal back to the person, in the words the workflow used.
     */
    public function transition(Request $request, Task $task): RedirectResponse
    {
        $data = $request->validate([
            'to' => ['required', 'string'],
            'note' => ['nullable', 'string', 'max:4000'],
        ]);

        $to = TaskStatus::tryFrom($data['to']);
        abort_if($to === null, 422, 'Unknown state.');

        // Rejecting work without saying why makes the engineer guess.
        if ($to === TaskStatus::ChangesRequested && blank($data['note'] ?? null)) {
            throw ValidationException::withMessages([
                'note' => 'Say what needs changing. A rejection with no note sends the task back with nothing to act on.',
            ]);
        }

        try {
            TaskTransition::apply($task, $to, $request->user(), $data['note'] ?? null);
        } catch (RuntimeException $e) {
            return back()->withErrors(['transition' => $e->getMessage()])->withInput();
        }

        return back()->with('status', 'Moved to '.$to->label().'.');
    }

    public function comment(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->project->mayBeSeenBy($request->user()), 403);

        $data = $request->validate(['body' => ['required', 'string', 'max:4000']]);

        $task->updates()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        ActivityEntry::record($task, 'commented', $request->user());

        return back()->with('status', 'Update posted.');
    }

    /**
     * References are project-scoped and sequential, so they can be quoted in a
     * commit message or a standup without ambiguity.
     */
    private function nextReference(Project $project): string
    {
        $n = $project->tasks()->count() + 1;

        return sprintf('%s-%03d', $project->code, $n);
    }
}
