<?php

namespace App\Models;

use Database\Factories\PillarFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pillar extends Model
{
    /** @use HasFactory<PillarFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'slug',
        'title',
        'nav_title',
        'descriptor',
        'thesis',
        'sort_order',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function insights(): HasMany
    {
        return $this->hasMany(Insight::class);
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Zero-padded pillar reference used as the structural marker in the UI.
     */
    public function reference(): string
    {
        return str_pad((string) $this->number, 2, '0', STR_PAD_LEFT);
    }
}
