<?php

namespace App\Models;

use Database\Factories\MetricFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Publication gate G-01. Every figure shown on the site carries the basis it rests
 * on, and stays unpublished until someone has supplied one.
 */
class Metric extends Model
{
    /** @use HasFactory<MetricFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'value',
        'basis',
        'effective_on',
        'substantiation_ref',
        'is_published',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'effective_on' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'key';
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
}
