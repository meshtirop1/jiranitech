<?php

namespace App\Models;

use Database\Factories\PlatformReferenceFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformReference extends Model
{
    /** @use HasFactory<PlatformReferenceFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'system_context',
        'scale_metrics',
        'stack',
        'operating_since',
        'cleared_for_disclosure',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scale_metrics' => 'array',
            'stack' => 'array',
            'cleared_for_disclosure' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Publication gate G-03. Scale characteristics are commercially sensitive and
     * stay hidden until the group has cleared them for external disclosure.
     */
    #[Scope]
    protected function disclosable(Builder $query): Builder
    {
        return $query->where('cleared_for_disclosure', true);
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
