<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PillarSeeder::class,
            EngagementModelSeeder::class,
            IndustrySeeder::class,
            PlatformReferenceSeeder::class,
            MetricSeeder::class,
            ComplianceClaimSeeder::class,
            InsightSeeder::class,
            TeamMemberSeeder::class,
            JobOpeningSeeder::class,
        ]);
    }
}
