<?php

namespace App\Models;

use App\Enums\SlaTier;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'pillar_id',
        'slug',
        'title',
        'executive_summary',
        'outcomes',
        'capabilities',
        'stack',
        'architecture_note',
        'sla_tier',
        'compliance_tags',
        'meta_title',
        'meta_description',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'outcomes' => 'array',
            'capabilities' => 'array',
            'stack' => 'array',
            'compliance_tags' => 'array',
            'sla_tier' => SlaTier::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
