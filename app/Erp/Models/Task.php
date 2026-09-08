<?php

namespace App\Erp\Models;

use App\Erp\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'project_id', 'reference', 'title', 'description', 'assignee_id', 'created_by',
        'status', 'weight', 'due_on', 'documentation', 'branch', 'pull_request_url',
        'merge_reference', 'blocked_reason', 'started_at', 'submitted_at', 'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'due_on' => 'date',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TaskReview::class)->latest();
    }

    public function updates(): HasMany
    {
        return $this->hasMany(TaskUpdate::class)->oldest();
    }

    #[Scope]
    protected function open(Builder $query): Builder
    {
        return $query->whereNotIn('status', [TaskStatus::Done->value, TaskStatus::Cancelled->value]);
    }

    #[Scope]
    protected function awaitingReview(Builder $query): Builder
    {
        return $query->where('status', TaskStatus::InReview->value);
    }

    #[Scope]
    protected function assignedTo(Builder $query, User $user): Builder
    {
        return $query->where('assignee_id', $user->id);
    }

    public function isOverdue(): bool
    {
        return $this->due_on !== null && $this->status->isOpen() && $this->due_on->isPast();
    }

    /** The most recent decision, which is what the assignee needs to read. */
    public function latestReview(): ?TaskReview
    {
        return $this->relationLoaded('reviews')
            ? $this->reviews->first()
            : $this->reviews()->first();
    }
}
