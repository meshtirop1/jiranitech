<?php

namespace App\Models;

use App\Enums\RfpTrack;
use Database\Factories\EngagementModelFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngagementModel extends Model
{
    /** @use HasFactory<EngagementModelFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'reference',
        'title',
        'summary',
        'commercial_basis',
        'scope_boundary',
        'governance_cadence',
        'suited_to',
        'rfp_track',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rfp_track' => RfpTrack::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
