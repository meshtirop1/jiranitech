<?php

namespace Database\Seeders;

use App\Models\PlatformReference;
use Illuminate\Database\Seeder;

/**
 * Publication gate G-03 from JTS-WEB-IA-001 section 4.
 *
 * Scale metrics are commercially sensitive. Rows ship with cleared_for_disclosure
 * false, so the qualitative operator argument renders while the figures stay hidden
 * until the group clears them in writing.
 */
class PlatformReferenceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->platforms() as $index => $platform) {
            PlatformReference::updateOrCreate(
                ['slug' => $platform['slug']],
                [...$platform, 'sort_order' => $index],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function platforms(): array
    {
        return [
            [
                'slug' => 'jiranisoko-marketplace',
                'title' => 'Jiranisoko Marketplace',
                'system_context' => 'A commerce and auction platform operated by Jiranisoko Market Ltd, carrying live payment rails, live fraud exposure and a live uptime obligation to trading merchants. It is the system against which this division sets its engineering standards, and the reason we describe ourselves as operators rather than only builders.',
                'scale_metrics' => [
                    ['label' => 'Registered merchants', 'value' => 'Pending disclosure clearance'],
                    ['label' => 'Monthly transaction volume', 'value' => 'Pending disclosure clearance'],
                    ['label' => 'Measured availability, trailing twelve months', 'value' => 'Pending disclosure clearance'],
                    ['label' => 'Peak concurrent bidding sessions', 'value' => 'Pending disclosure clearance'],
                ],
                'stack' => ['Laravel', 'MySQL', 'Redis', 'M-Pesa Daraja API', 'Cloudflare', 'Linux'],
                'operating_since' => null,
                'cleared_for_disclosure' => false,
            ],
            [
                'slug' => 'group-technology-portfolio',
                'title' => 'Group Technology Portfolio',
                'system_context' => 'Jiranisoko Market Ltd holds operating interests across several digital verticals. The technology division architects, builds and operates the systems behind them, which is what gives our delivery standards their enforcement mechanism: they are the standards our own group depends on.',
                'scale_metrics' => [
                    ['label' => 'Platforms in production', 'value' => 'Pending disclosure clearance'],
                    ['label' => 'Verticals served', 'value' => 'Pending disclosure clearance'],
                ],
                'stack' => ['Laravel', '.NET', 'Angular', 'PostgreSQL', 'MySQL', 'Kubernetes'],
                'operating_since' => null,
                'cleared_for_disclosure' => false,
            ],
        ];
    }
}
