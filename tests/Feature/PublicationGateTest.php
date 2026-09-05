<?php

namespace Tests\Feature;

use App\Enums\ComplianceStatus;
use App\Models\ComplianceClaim;
use App\Models\Metric;
use App\Models\PlatformReference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The publication gates from JTS-WEB-IA-001 section 4 are enforced in code, not by
 * editorial discipline. These tests exist so that a later change cannot quietly turn an
 * unsubstantiated figure or an uncertified standard into a published claim.
 */
class PublicationGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_g01_unpublished_metrics_do_not_reach_the_homepage(): void
    {
        $this->seed();

        $gated = Metric::query()->where('key', 'contracted-availability')->sole();

        $this->assertFalse($gated->is_published);

        // The figure itself also appears in the SLA table, which carries its own gate
        // (G-05). What must not appear is the metric bar entry asserting it as a fact
        // about this firm, so the label is what this test pins.
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Engineering disciplines')
            ->assertDontSee('Contracted availability, Platinum tier');
    }

    public function test_g01_a_metric_becomes_visible_once_published(): void
    {
        $this->seed();

        // The hero bar carries the first four published metrics by sort order, so the
        // newly substantiated figure is promoted into that set as well as published.
        Metric::query()->where('key', 'contracted-availability')->update([
            'is_published' => true,
            'basis' => 'Ratified against provider composite SLA on 2026-09-01.',
            'substantiation_ref' => 'BOARD-2026-114',
            'sort_order' => 0,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Contracted availability, Platinum tier');
    }

    public function test_g02_a_standard_we_have_not_certified_renders_as_aligned(): void
    {
        $this->seed();

        $this->assertSame(
            0,
            ComplianceClaim::query()->where('status', ComplianceStatus::Certified)->count(),
            'No compliance claim may ship seeded as certified.',
        );

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Aligned — not certified', escape: false);
    }

    public function test_g03_platform_scale_metrics_stay_hidden_until_cleared(): void
    {
        $this->seed();

        $platform = PlatformReference::query()->where('slug', 'jiranisoko-marketplace')->sole();

        $this->assertFalse($platform->cleared_for_disclosure);

        $this->get(route('platforms.show', $platform))
            ->assertOk()
            ->assertSee('Publication gate G-03')
            ->assertDontSee('Registered merchants');
    }

    public function test_g03_scale_metrics_render_once_cleared(): void
    {
        $this->seed();

        $platform = PlatformReference::query()->where('slug', 'jiranisoko-marketplace')->sole();
        $platform->update([
            'cleared_for_disclosure' => true,
            'scale_metrics' => [['label' => 'Registered merchants', 'value' => '12,400']],
        ]);

        $this->get(route('platforms.show', $platform))
            ->assertOk()
            ->assertSee('Registered merchants')
            ->assertDontSee('Publication gate G-03');
    }

    public function test_g07_footer_flags_missing_corporate_identity(): void
    {
        $this->seed();

        config([
            'company.registered_address' => null,
            'company.parent.registration_number' => null,
        ]);

        $this->get(route('home'))->assertOk()->assertSee('Gate G-07');
    }

    public function test_g07_footer_shows_corporate_identity_once_configured(): void
    {
        $this->seed();

        config([
            'company.registered_address' => 'Kenyatta Street, Eldoret',
            'company.parent.registration_number' => 'PVT-ABC1234',
            'company.email.enquiries' => 'enquiries@example.test',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('PVT-ABC1234')
            ->assertDontSee('Gate G-07');
    }
}
