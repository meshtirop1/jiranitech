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
 *
 * Note the entity boundary: these are platforms the holding company operates, and
 * they are siblings of this division rather than its parent. The external link lives
 * on the platform record so that a link to the marketplace can never be mistaken for
 * a link to Jiranisoko Market Ltd itself.
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
                'title' => 'JiraniSoko Marketplace',
                'system_context' => 'A consumer marketplace operated by Jiranisoko Market Ltd, covering listings, local jobs, vehicles, property and delivery across Kenya. It carries live M-Pesa payment rails, live fraud exposure and a live uptime obligation to the people trading on it. It is the system against which this division sets its engineering standards, and the reason we describe ourselves as operators rather than only builders.',
                'scale_metrics' => [
                    ['label' => 'Registered users', 'value' => 'Pending disclosure clearance'],
                    ['label' => 'Monthly transaction volume', 'value' => 'Pending disclosure clearance'],
                    ['label' => 'Measured availability, trailing twelve months', 'value' => 'Pending disclosure clearance'],
                    ['label' => 'Peak concurrent sessions', 'value' => 'Pending disclosure clearance'],
                ],
                'stack' => ['Laravel', 'MySQL', 'Redis', 'M-Pesa Daraja API', 'Cloudflare', 'Linux'],
                'operating_since' => null,
                'external_url' => 'https://jiranisoko.com',
                'external_label' => 'Visit the marketplace',
                'cleared_for_disclosure' => false,
            ],
            [
                'slug' => 'group-technology-portfolio',
                'title' => 'Group Technology Portfolio',
                'system_context' => 'Jiranisoko Market Ltd holds operating interests across several digital verticals. This division architects, builds and operates the systems behind them, which is what gives our delivery standards their enforcement mechanism: they are the standards our own group depends on.',
                'scale_metrics' => [
                    ['label' => 'Platforms in production', 'value' => 'Pending disclosure clearance'],
                    ['label' => 'Verticals served', 'value' => 'Pending disclosure clearance'],
                ],
                'stack' => ['Laravel', '.NET', 'Angular', 'PostgreSQL', 'MySQL', 'Kubernetes'],
                'operating_since' => null,
                'external_url' => null,
                'external_label' => null,
                'cleared_for_disclosure' => false,
            ],
        ];
    }
}
