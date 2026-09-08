<?php

namespace App\Erp\Models;

use App\Erp\Enums\ProjectRole;
use App\Erp\Enums\ProjectStatus;
use App\Erp\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Project extends Model
{
    protected $fillable = [
        'code', 'name', 'slug', 'client', 'status', 'summary',
        'repository_url', 'default_branch', 'started_on', 'target_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'started_on' => 'date',
            'target_date' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    #[Scope]
    protected function running(Builder $query): Builder
    {
        return $query->whereIn('status', [ProjectStatus::Discovery->value, ProjectStatus::Active->value]);
    }

    /** Projects this person can see: theirs, or everything for a director. */
    #[Scope]
    protected function visibleTo(Builder $query, User $user): Builder
    {
        if ($user->erpRole()?->seesEverything()) {
            return $query;
        }

        return $query->whereHas('members', fn (Builder $q) => $q->whereKey($user->id));
    }

    public function roleOf(User $user): ?ProjectRole
    {
        $member = $this->relationLoaded('members')
            ? $this->members->firstWhere('id', $user->id)
            : $this->members()->whereKey($user->id)->first();

        return $member ? ProjectRole::from($member->pivot->role) : null;
    }

    public function mayBeSeenBy(User $user): bool
    {
        return (bool) $user->erpRole()?->seesEverything() || $this->roleOf($user) !== null;
    }

    /**
     * A director may review anywhere. That is not a convenience: it is what makes
     * the "nobody reviews their own work" rule survivable on a project whose only
     * reviewer is the person who wrote the code.
     */
    public function mayBeReviewedBy(User $user): bool
    {
        return (bool) $user->erpRole()?->seesEverything() || (bool) $this->roleOf($user)?->mayReview();
    }

    public function mayBeDirectedBy(User $user): bool
    {
        return (bool) $user->erpRole()?->administersDelivery() || (bool) $this->roleOf($user)?->mayDirect();
    }

    public function lead(): ?User
    {
        return $this->members->first(fn (User $u) => $u->pivot->role === ProjectRole::Lead->value);
    }

    /**
     * Progress, computed from the state of the work.
     *
     * Never a stored figure. A percentage somebody types is optimistic on the day
     * it is entered and wrong by the next, and the whole point of this system is
     * that a lead can trust the number without asking anyone.
     *
     * @return array{done:int, total:int, percent:int, counts:Collection<string,int>}
     */
    public function progress(): array
    {
        $tasks = $this->relationLoaded('tasks') ? $this->tasks : $this->tasks()->get();

        $counted = $tasks->reject(fn (Task $t) => $t->status->isCancelled());
        $total = (int) $counted->sum('weight');
        $done = (int) $counted->filter(fn (Task $t) => $t->status->isComplete())->sum('weight');

        return [
            'done' => $done,
            'total' => $total,
            'percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
            'counts' => $tasks->groupBy(fn (Task $t) => $t->status->value)->map->count(),
        ];
    }

    /**
     * Work a reviewer is holding up on this project.
     *
     * @return Collection<int, Task>
     */
    public function awaitingReview(): Collection
    {
        $tasks = $this->relationLoaded('tasks') ? $this->tasks : $this->tasks()->get();

        return $tasks->filter(fn (Task $t) => $t->status === TaskStatus::InReview);
    }

    public function isOverdue(): bool
    {
        return $this->target_date !== null
            && $this->status->isRunning()
            && $this->target_date->isPast();
    }
}
