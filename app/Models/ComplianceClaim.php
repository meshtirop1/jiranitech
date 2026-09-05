<?php

namespace App\Models;

use App\Enums\ComplianceStatus;
use Database\Factories\ComplianceClaimFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceClaim extends Model
{
    /** @use HasFactory<ComplianceClaimFactory> */
    use HasFactory;

    protected $fillable = [
        'standard',
        'status',
        'evidence_url',
        'reviewed_on',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ComplianceStatus::class,
            'reviewed_on' => 'date',
        ];
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
