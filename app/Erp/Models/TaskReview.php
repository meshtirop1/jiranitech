<?php

namespace App\Erp\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One reviewer's decision on one submission.
 *
 * Its own record rather than a column on the task, because a task is usually
 * reviewed more than once and the history of what was asked for is the useful
 * part — both to the engineer and to anyone auditing the engagement later.
 */
class TaskReview extends Model
{
    public const APPROVED = 'approved';

    public const CHANGES_REQUESTED = 'changes_requested';

    protected $fillable = ['task_id', 'reviewer_id', 'decision', 'notes'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function approved(): bool
    {
        return $this->decision === self::APPROVED;
    }
}
