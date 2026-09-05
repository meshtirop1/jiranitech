<?php

namespace Tests\Feature;

use App\Models\JobOpening;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_leadership_page_states_every_post_and_its_accountability(): void
    {
        $this->seed();

        $response = $this->get(route('company.leadership'))->assertOk();

        foreach ([
            'Managing Director',
            'Chief Technology Officer',
            'Director of Delivery',
            'Head of Information Security',
            'Data Protection Officer',
            'Practice Lead — Artificial Intelligence &amp; Automation',
            'Practice Lead — Financial Technology &amp; Payments',
            'Practice Lead — Cloud Infrastructure &amp; DevOps',
        ] as $post) {
            $response->assertSee($post, escape: false);
        }

        $response->assertSee('One escalation path');
    }

    public function test_g09_an_unannounced_post_shows_the_vacancy_rather_than_a_name(): void
    {
        $this->seed();

        $this->assertSame(8, TeamMember::query()->whereNull('name')->count());

        $this->get(route('company.leadership'))
            ->assertOk()
            ->assertSee('Appointment to be announced')
            ->assertSee('Publication gate G-09 — 8 of 8 outstanding', escape: false);
    }

    public function test_g09_the_gate_note_clears_once_every_post_is_filled(): void
    {
        $this->seed();

        TeamMember::query()->update(['name' => 'A. Kiprotich']);

        $this->get(route('company.leadership'))
            ->assertOk()
            ->assertSee('A. Kiprotich')
            ->assertDontSee('Appointment to be announced')
            ->assertDontSee('Publication gate G-09');
    }

    public function test_g10_job_listings_ship_unpublished_and_the_page_shows_its_empty_state(): void
    {
        $this->seed();

        $this->assertSame(4, JobOpening::query()->count());
        $this->assertSame(0, JobOpening::query()->published()->count());

        $this->get(route('company.careers'))
            ->assertOk()
            ->assertSee('No advertised vacancies right now')
            ->assertSee('Publication gate G-10 — 4 listing(s) held back', escape: false)
            ->assertDontSee('Senior Payments Engineer');
    }

    public function test_g10_a_confirmed_role_appears_once_published(): void
    {
        $this->seed();

        JobOpening::query()->where('slug', 'senior-payments-engineer')->update([
            'is_published' => true,
            'posted_at' => now(),
        ]);

        $this->get(route('company.careers'))
            ->assertOk()
            ->assertSee('Senior Payments Engineer')
            ->assertSee('Hybrid — three days on site', escape: false)
            ->assertDontSee('No advertised vacancies right now')
            ->assertSee('Publication gate G-10 — 3 listing(s) held back', escape: false);
    }

    public function test_the_careers_page_renders_the_devcom_section_sequence(): void
    {
        $this->seed();

        $response = $this->get(route('company.careers'))->assertOk();

        foreach ([
            'What you would actually work on',
            'Why join us',
            'The engineering ladder',
            'How we hire',
            'Open positions',
            'Come and build things that matter',
        ] as $heading) {
            $response->assertSee($heading);
        }

        // Four numbered process steps, matching the grid the stylesheet pins.
        $this->assertSame(4, substr_count($response->getContent(), 'class="step__num"'));
    }

    public function test_both_pages_use_the_overlay_hero_and_breadcrumb_strip(): void
    {
        $this->seed();

        foreach ([route('company.leadership'), route('company.careers')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('class="pagehero"', escape: false)
                ->assertSee('class="crumbbar"', escape: false)
                ->assertSee('class="blockhead"', escape: false);
        }
    }
}
