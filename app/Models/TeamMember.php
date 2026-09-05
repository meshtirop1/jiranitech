<?php

namespace App\Models;

use Database\Factories\TeamMemberFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    /** @use HasFactory<TeamMemberFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'role_title',
        'discipline',
        'accountability',
        'remit',
        'photo_path',
        'is_published',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'remit' => 'array',
            'is_published' => 'boolean',
        ];
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Publication gate G-09. A post whose holder has not been announced still states
     * its accountability; the card shows the vacancy rather than inventing a person.
     */
    public function isAnnounced(): bool
    {
        return filled($this->name);
    }

    public function initials(): string
    {
        if (! $this->isAnnounced()) {
            return '—';
        }

        return collect(explode(' ', (string) $this->name))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
