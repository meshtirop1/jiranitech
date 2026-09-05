<?php

namespace Database\Seeders;

use App\Models\Metric;
use Illuminate\Database\Seeder;

/**
 * Publication gate G-01 from JTS-WEB-IA-001 section 4.
 *
 * Metrics whose value is a matter of record ship published. Metrics that assert a
 * commercial or contractual position ship unpublished with the outstanding gate named
 * in their basis, so the site cannot state them until someone has substantiated them.
 */
class MetricSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->metrics() as $index => $metric) {
            Metric::updateOrCreate(
                ['key' => $metric['key']],
                [...$metric, 'sort_order' => $index],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function metrics(): array
    {
        return [
            [
                'key' => 'operating-headquarters',
                'label' => 'Operating headquarters',
                'value' => 'Eldoret, Kenya',
                'basis' => 'Registered operating address of the division.',
                'effective_on' => '2026-09-04',
                'substantiation_ref' => 'Certificate of incorporation',
                'is_published' => true,
            ],
            [
                'key' => 'operating-timezone',
                'label' => 'East Africa Time',
                'value' => 'UTC+3',
                'basis' => 'Standard offset. Full working-day overlap with the United Kingdom, continental Europe and the Gulf.',
                'effective_on' => '2026-09-04',
                'substantiation_ref' => 'IANA tz database, Africa/Nairobi',
                'is_published' => true,
            ],
            [
                'key' => 'engineering-disciplines',
                'label' => 'Engineering disciplines',
                'value' => '6',
                'basis' => 'Capability taxonomy published at JTS-WEB-IA-001 section 2.3.',
                'effective_on' => '2026-09-04',
                'substantiation_ref' => 'JTS-WEB-IA-001 §2.3',
                'is_published' => true,
            ],
            [
                'key' => 'specialist-service-lines',
                'label' => 'Specialist service lines',
                'value' => '22',
                'basis' => 'Leaf services in the published capability taxonomy.',
                'effective_on' => '2026-09-04',
                'substantiation_ref' => 'JTS-WEB-IA-001 §2.3',
                'is_published' => true,
            ],
            [
                'key' => 'contracted-availability',
                'label' => 'Contracted availability, Platinum tier',
                'value' => '99.95%',
                'basis' => 'GATE G-05 OUTSTANDING. Target not yet ratified against the composite SLA of the underlying cloud providers. Do not publish until Delivery and Legal confirm the figure is achievable and contractually bound.',
                'effective_on' => '2026-09-04',
                'substantiation_ref' => null,
                'is_published' => false,
            ],
            [
                'key' => 'group-platforms-in-production',
                'label' => 'Group platforms in production',
                'value' => '2',
                'basis' => 'GATE G-03 OUTSTANDING. Awaiting written clearance from Jiranisoko Market Ltd that the platform count and its scale characteristics may be disclosed externally.',
                'effective_on' => '2026-09-04',
                'substantiation_ref' => null,
                'is_published' => false,
            ],
        ];
    }
}
