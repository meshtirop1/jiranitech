<?php

namespace App\Models;

use Database\Factories\JobOpeningFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOpening extends Model
{
    /** @use HasFactory<JobOpeningFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'level',
        'discipline',
        'location',
        'arrangement',
        'summary',
        'responsibilities',
        'requirements',
        'is_published',
        'posted_at',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'responsibilities' => 'array',
            'requirements' => 'array',
            'is_published' => 'boolean',
            'posted_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
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
