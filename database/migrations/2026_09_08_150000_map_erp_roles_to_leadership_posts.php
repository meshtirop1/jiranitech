<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Moves the three generic roles onto the posts published at /company/leadership.
 *
 * The first cut used engineer / lead / director, which was a permissions
 * hierarchy rather than the org chart. Existing accounts are mapped to their
 * nearest published post; nobody gains authority in the process.
 */
return new class extends Migration
{
    private const FORWARD = [
        'director' => 'managing_director',
        'lead' => 'practice_lead',
        'engineer' => 'engineer',
    ];

    public function up(): void
    {
        foreach (self::FORWARD as $old => $new) {
            DB::table('users')->where('erp_role', $old)->update(['erp_role' => $new]);
        }
    }

    public function down(): void
    {
        // Several posts collapse onto one old value, so this is deliberately
        // lossy in the direction that cannot grant anyone more than they had.
        $back = [
            'managing_director' => 'director',
            'chief_technology_officer' => 'director',
            'director_of_delivery' => 'director',
            'head_of_information_security' => 'lead',
            'data_protection_officer' => 'lead',
            'practice_lead' => 'lead',
            'engineer' => 'engineer',
        ];

        foreach ($back as $new => $old) {
            DB::table('users')->where('erp_role', $new)->update(['erp_role' => $old]);
        }
    }
};
