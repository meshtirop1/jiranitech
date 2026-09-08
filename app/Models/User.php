<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Erp\Enums\ErpRole;
use App\Erp\Enums\TaskStatus;
use App\Erp\Models\Project;
use App\Erp\Models\Task;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Fillable(['name', 'email', 'password', 'is_admin', 'erp_role', 'job_title', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'erp_role' => ErpRole::class,
        ];
    }

    // --- delivery system ----------------------------------------------------

    public function erpRole(): ?ErpRole
    {
        return $this->erp_role;
    }

    /** Whether this account may sign in to the delivery system at all. */
    public function worksInDelivery(): bool
    {
        return $this->erp_role !== null && $this->is_active;
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    #[Scope]
    protected function inDelivery(Builder $query): Builder
    {
        return $query->whereNotNull('erp_role')->where('is_active', true);
    }

    /**
     * Work this person is currently holding — what their own dashboard opens on.
     *
     * @return Collection<int, Task>
     */
    public function openWork(): Collection
    {
        return $this->assignedTasks()
            ->open()
            ->with('project')
            ->orderByRaw("CASE status
                WHEN 'changes_requested' THEN 1
                WHEN 'blocked' THEN 2
                WHEN 'in_progress' THEN 3
                WHEN 'in_review' THEN 4
                ELSE 5 END")
            ->orderBy('due_on')
            ->get();
    }

    /**
     * Submissions waiting on this person to review.
     *
     * Excludes their own work, because they cannot review it — showing it in the
     * queue would only be a promise the system then refuses to keep.
     *
     * @return Collection<int, Task>
     */
    public function reviewQueue(): Collection
    {
        $projects = $this->erpRole()?->seesEverything()
            ? Project::query()->pluck('id')
            : $this->projects()
                ->wherePivotIn('role', ['lead', 'reviewer'])
                ->pluck('projects.id');

        return Task::query()
            ->whereIn('project_id', $projects)
            ->where('status', TaskStatus::InReview->value)
            ->where(fn (Builder $q) => $q->whereNull('assignee_id')->orWhere('assignee_id', '!=', $this->id))
            ->with(['project', 'assignee'])
            ->orderBy('submitted_at')
            ->get();
    }
}
