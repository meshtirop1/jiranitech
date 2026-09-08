<?php

namespace App\Erp\Support;

use App\Erp\Enums\TaskStatus;
use App\Erp\Models\Task;
use App\Models\User;

/**
 * The rules governing how work moves.
 *
 * Every transition in the system goes through here, so the workflow is one
 * readable thing rather than a condition scattered across controllers and
 * templates. If a rule is not in this file, it is not a rule.
 *
 * Three of these are deliberate governance controls rather than conveniences:
 *
 *   1. A task cannot be submitted for review without its documentation written
 *      and its branch named. Documentation that is requested but not required
 *      does not get written, and then the person who wrote the code leaves.
 *
 *   2. Nobody may review their own work — including a lead. When the lead is the
 *      assignee the review escalates to another reviewer on the project or to
 *      leadership. This is the same separation of duties the company profile
 *      claims, applied to itself.
 *
 *   3. Blocking requires a reason. "Blocked" without a cause is invisible to the
 *      person who could clear it.
 */
class TaskWorkflow
{
    /**
     * Which states may follow which.
     *
     * @return array<string, array<int, TaskStatus>>
     */
    public static function map(): array
    {
        return [
            TaskStatus::Backlog->value => [TaskStatus::InProgress, TaskStatus::Cancelled],
            TaskStatus::InProgress->value => [TaskStatus::InReview, TaskStatus::Blocked, TaskStatus::Backlog, TaskStatus::Cancelled],
            TaskStatus::Blocked->value => [TaskStatus::InProgress, TaskStatus::Cancelled],
            TaskStatus::InReview->value => [TaskStatus::Approved, TaskStatus::ChangesRequested],
            TaskStatus::ChangesRequested->value => [TaskStatus::InReview, TaskStatus::InProgress, TaskStatus::Cancelled],
            TaskStatus::Approved->value => [TaskStatus::Done, TaskStatus::ChangesRequested],
            TaskStatus::Done->value => [],
            TaskStatus::Cancelled->value => [],
        ];
    }

    /**
     * Why a transition is refused, or null when it is allowed.
     *
     * Returning the reason rather than a boolean is the point: the interface can
     * then tell an engineer what is missing instead of hiding the button.
     */
    public static function refusal(Task $task, TaskStatus $to, User $actor): ?string
    {
        $from = $task->status;

        if ($from === $to) {
            return 'The task is already '.$to->label().'.';
        }

        if (! in_array($to, self::map()[$from->value] ?? [], true)) {
            return sprintf('%s cannot move straight to %s.', $from->label(), $to->label());
        }

        $isAssignee = $task->assignee_id !== null && $task->assignee_id === $actor->id;
        $mayReview = $task->project->mayBeReviewedBy($actor);
        $mayDirect = $task->project->mayBeDirectedBy($actor);

        return match ($to) {
            TaskStatus::InProgress => (! $isAssignee && ! $mayDirect)
                ? 'Only the assignee or the project lead can start this task.'
                : null,

            TaskStatus::Blocked => (! $isAssignee && ! $mayDirect)
                ? 'Only the assignee or the project lead can block this task.'
                : (blank($task->blocked_reason) ? 'Say what is blocking it. A blocked task with no reason cannot be cleared by anyone else.' : null),

            TaskStatus::InReview => self::refuseSubmission($task, $actor, $isAssignee),

            TaskStatus::Approved, TaskStatus::ChangesRequested => self::refuseReview($task, $actor, $isAssignee, $mayReview),

            TaskStatus::Done => (! $mayReview && ! $isAssignee)
                ? 'Only the assignee or a reviewer can record the merge.'
                : null,

            TaskStatus::Backlog, TaskStatus::Cancelled => $mayDirect
                ? null
                : 'Only the project lead can return a task to the backlog or cancel it.',
        };
    }

    private static function refuseSubmission(Task $task, User $actor, bool $isAssignee): ?string
    {
        if (! $isAssignee) {
            return 'Only the assignee submits their own work for review.';
        }

        if (blank($task->documentation)) {
            return 'Write the documentation first. A reviewer cannot judge what was done from a diff alone, and nobody writes it afterwards.';
        }

        if (blank($task->branch)) {
            return 'Name the branch the work is on, so the reviewer knows what to look at.';
        }

        return null;
    }

    private static function refuseReview(Task $task, User $actor, bool $isAssignee, bool $mayReview): ?string
    {
        if ($isAssignee) {
            return 'You cannot review your own work. Ask another reviewer on this project, or a director.';
        }

        if (! $mayReview) {
            return 'Only a reviewer on this project, or leadership, can accept or reject this work.';
        }

        return null;
    }

    public static function allows(Task $task, TaskStatus $to, User $actor): bool
    {
        return self::refusal($task, $to, $actor) === null;
    }

    /**
     * The transitions this person could make right now, for rendering controls.
     *
     * @return array<int, TaskStatus>
     */
    public static function availableTo(Task $task, User $actor): array
    {
        return array_values(array_filter(
            self::map()[$task->status->value] ?? [],
            fn (TaskStatus $s) => self::allows($task, $s, $actor),
        ));
    }

    /**
     * What is stopping this task moving forward, phrased for the person looking
     * at it. Null when nothing is.
     */
    public static function blockingReason(Task $task, User $actor): ?string
    {
        $forward = match ($task->status) {
            TaskStatus::Backlog, TaskStatus::Blocked, TaskStatus::ChangesRequested => TaskStatus::InProgress,
            TaskStatus::InProgress => TaskStatus::InReview,
            TaskStatus::InReview => TaskStatus::Approved,
            TaskStatus::Approved => TaskStatus::Done,
            default => null,
        };

        return $forward ? self::refusal($task, $forward, $actor) : null;
    }
}
