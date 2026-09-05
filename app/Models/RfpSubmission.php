<?php

namespace App\Models;

use App\Enums\RfpTrack;
use Database\Factories\RfpSubmissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfpSubmission extends Model
{
    /** @use HasFactory<RfpSubmissionFactory> */
    use HasFactory;

    protected $fillable = [
        'reference',
        'track',
        'organisation',
        'contact_name',
        'role',
        'email',
        'telephone',
        'country',
        'budget_band',
        'timeline',
        'scope_summary',
        'service_interests',
        'nda_required',
        'source_page',
        'submitted_ip',
        'acknowledged_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'track' => RfpTrack::class,
            'service_interests' => 'array',
            'nda_required' => 'boolean',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }
}
