<?php

namespace App\Erp\Enums;

/**
 * The states a work item can be in.
 *
 * Deliberately small. Every additional column on a board is a place for work to
 * sit unowned, and the question a delivery lead actually needs answered is
 * "whose move is it?" — which these eight states answer without ambiguity.
 */
enum TaskStatus: string
{
    case Backlog = 'backlog';
    case InProgress = 'in_progress';
    case Blocked = 'blocked';
    case InReview = 'in_review';
    case ChangesRequested = 'changes_requested';
    case Approved = 'approved';
    case Done = 'done';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Backlog => 'Backlog',
            self::InProgress => 'In progress',
            self::Blocked => 'Blocked',
            self::InReview => 'In review',
            self::ChangesRequested => 'Changes requested',
            self::Approved => 'Approved',
            self::Done => 'Done',
            self::Cancelled => 'Cancelled',
        };
    }

    /** Whose move it is, in plain words. This is the column a lead reads first. */
    public function waitingOn(): string
    {
        return match ($this) {
            self::Backlog => 'Unstarted',
            self::InProgress => 'The assignee',
            self::Blocked => 'Whoever can unblock it',
            self::InReview => 'The reviewer',
            self::ChangesRequested => 'The assignee',
            self::Approved => 'Merge',
            self::Done, self::Cancelled => 'Nobody',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Done, self::Approved => 'positive',
            self::Blocked, self::ChangesRequested => 'negative',
            self::InReview => 'attention',
            self::Cancelled => 'muted',
            default => 'neutral',
        };
    }

    /** Counts towards delivered work when progress is computed. */
    public function isComplete(): bool
    {
        return $this === self::Done;
    }

    /** Excluded from the denominator: cancelled work was never owed. */
    public function isCancelled(): bool
    {
        return $this === self::Cancelled;
    }

    public function isOpen(): bool
    {
        return ! $this->isComplete() && ! $this->isCancelled();
    }

    /**
     * Order for boards and reports: the states needing attention first.
     *
     * @return array<int, self>
     */
    public static function boardOrder(): array
    {
        return [
            self::Blocked,
            self::ChangesRequested,
            self::InReview,
            self::InProgress,
            self::Approved,
            self::Backlog,
            self::Done,
        ];
    }
}
