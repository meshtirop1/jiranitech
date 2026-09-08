<?php

namespace App\Erp\Support;

use App\Erp\Enums\TaskStatus;
use App\Erp\Models\ActivityEntry;
use App\Erp\Models\Task;
use App\Erp\Models\TaskReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Applies a state change, or refuses it.
 *
 * Every move goes through here so that three things always happen together and
 * cannot drift apart: the rule is checked, the timestamps are set, and the
 * activity trail is written. A controller that set a status directly would
 * quietly break all three.
 */
class TaskTransition
{
    /**
     * @throws RuntimeException when the workflow refuses the move
     */
    public static function apply(Task $task, TaskStatus $to, User $actor, ?string $note = null): Task
    {
        $refusal = TaskWorkflow::refusal($task, $to, $actor);

        if ($refusal !== null) {
            throw new RuntimeException($refusal);
        }

        $from = $task->status;

        return DB::transaction(function () use ($task, $from, $to, $actor, $note) {
            $task->status = $to;

            match ($to) {
                TaskStatus::InProgress => self::onStart($task),
                TaskStatus::InReview => self::onSubmit($task),
                TaskStatus::Approved => self::onApprove($task, $actor, $note),
                TaskStatus::ChangesRequested => self::onChangesRequested($task, $actor, $note),
                TaskStatus::Done => self::onDone($task, $note),
                TaskStatus::Backlog => self::onReset($task),
                default => null,
            };

            $task->save();

            ActivityEntry::record(
                $task,
                'transitioned',
                $actor,
                $from->value,
                $to->value,
                $note,
            );

            return $task->refresh();
        });
    }

    private static function onStart(Task $task): void
    {
        $task->started_at ??= now();
        $task->blocked_reason = null;
    }

    private static function onSubmit(Task $task): void
    {
        $task->submitted_at = now();
    }

    private static function onApprove(Task $task, User $actor, ?string $note): void
    {
        TaskReview::create([
            'task_id' => $task->id,
            'reviewer_id' => $actor->id,
            'decision' => TaskReview::APPROVED,
            'notes' => $note,
        ]);
    }

    private static function onChangesRequested(Task $task, User $actor, ?string $note): void
    {
        TaskReview::create([
            'task_id' => $task->id,
            'reviewer_id' => $actor->id,
            'decision' => TaskReview::CHANGES_REQUESTED,
            'notes' => $note,
        ]);
    }

    private static function onDone(Task $task, ?string $note): void
    {
        $task->completed_at = now();

        // The merge reference is what ties this row to the repository. Recorded
        // as free text because the note carries a commit id or a merge request
        // number depending on where the project lives.
        if (filled($note)) {
            $task->merge_reference = $note;
        }
    }

    private static function onReset(Task $task): void
    {
        $task->started_at = null;
        $task->submitted_at = null;
        $task->blocked_reason = null;
    }
}
