<?php

namespace App\Erp\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Append-only record of what happened.
 *
 * The value of this table is precisely that it cannot be tidied: months after an
 * engagement closes, the question asked is "who accepted this, and when", and an
 * answer that could have been edited is not an answer. Nothing in the
 * application updates or deletes a row here, and the model refuses both.
 */
class ActivityEntry extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'subject_type', 'subject_id', 'action',
        'from_state', 'to_state', 'note',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new \RuntimeException('Activity entries are append-only.'));
        static::deleting(fn () => throw new \RuntimeException('Activity entries are append-only.'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Record something. The only way a row gets written.
     */
    public static function record(
        Model $subject,
        string $action,
        ?User $actor = null,
        ?string $from = null,
        ?string $to = null,
        ?string $note = null,
    ): self {
        return self::create([
            'user_id' => $actor?->id,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'action' => $action,
            'from_state' => $from,
            'to_state' => $to,
            'note' => $note,
            'created_at' => now(),
        ]);
    }

    /** Human sentence for the timeline. */
    public function describe(): string
    {
        $who = $this->user?->name ?? 'Someone';

        return match ($this->action) {
            'created' => "{$who} created this",
            'transitioned' => sprintf('%s moved it from %s to %s', $who, self::pretty($this->from_state), self::pretty($this->to_state)),
            'reviewed' => "{$who} reviewed it",
            'assigned' => "{$who} changed the assignee",
            'commented' => "{$who} added an update",
            'documented' => "{$who} updated the documentation",
            default => "{$who} {$this->action}",
        };
    }

    private static function pretty(?string $state): string
    {
        return $state ? str_replace('_', ' ', $state) : 'nothing';
    }
}
