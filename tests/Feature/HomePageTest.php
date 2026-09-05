<?php

namespace Tests\Feature;

use App\Models\Insight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_every_narrative_section(): void
    {
        $this->seed();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('We build the platforms institutions run on.')
            ->assertSee('Standards and frameworks governing our delivery', escape: false)
            ->assertSee('We do not only build platforms. We operate them.')
            ->assertSee('Six engineering disciplines. One accountable delivery organisation.')
            ->assertSee('Three contracting structures.', escape: false)
            ->assertSee('Global engineering standard. Regional economic advantage.')
            ->assertSee('Contractual commitments, not aspirations.')
            ->assertSee('Domain constraints we already work inside.')
            ->assertSee('Begin with a scoped conversation, not a sales call.');
    }

    public function test_it_lists_all_six_pillars_with_their_service_lines(): void
    {
        $this->seed();

        $response = $this->get(route('home'))->assertOk();

        foreach ([
            'Artificial Intelligence &amp; Automation',
            'Cloud Infrastructure &amp; DevOps',
            'Enterprise Software &amp; SaaS Solutions',
            'Financial Technology &amp; Payment Ecosystems',
            'Core Frameworks &amp; API Engineering',
            'Decentralised Web3 &amp; Blockchain Engineering',
        ] as $pillar) {
            $response->assertSee($pillar, escape: false);
        }

        $response->assertSee('Agentic AI &amp; Autonomous Agent Architectures', escape: false);
    }

    public function test_the_insights_rail_suppresses_itself_below_three_published_items(): void
    {
        $this->seed();

        $this->get(route('home'))->assertOk()->assertSee('Our reasoning, published.');

        Insight::query()->latest('id')->take(1)->update(['published_at' => null]);

        $this->get(route('home'))->assertOk()->assertDontSee('Our reasoning, published.');
    }

    public function test_the_layout_emits_the_self_hosted_font_face_block(): void
    {
        $this->seed();

        // Vite builds the woff2 files whether or not anything references them, so a
        // missing Vite::fonts() call fails silently into a system-font fallback. This
        // asserts the font actually reaches the page.
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('PT Sans', escape: false)
            ->assertSee('@font-face', escape: false);
    }

    public function test_the_timezone_figure_states_the_us_overlap_accurately(): void
    {
        $this->seed();

        // The blueprint originally claimed three hours. On 08:30-17:30 EAT against a
        // 09:00-17:00 Eastern working day the overlap is 1.5 hours, and the page says so.
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('the overlap is 1.5 hours')
            ->assertSee('staggered afternoon shift');
    }
}
