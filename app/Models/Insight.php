<?php

namespace App\Models;

use App\Enums\InsightFormat;
use Database\Factories\InsightFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Insight extends Model
{
    /** @use HasFactory<InsightFactory> */
    use HasFactory;

    protected $fillable = [
        'pillar_id',
        'format',
        'slug',
        'title',
        'abstract_line',
        'body',
        'read_minutes',
        'is_featured',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'format' => InsightFormat::class,
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
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
    protected function published(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    #[Scope]
    protected function latestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }
}
