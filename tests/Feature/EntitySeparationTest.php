<?php

namespace Tests\Feature;

use App\Models\PlatformReference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Three entities that must not be conflated:
 *
 *   Jiranisoko Market Ltd       the holding company, PVT-YQ195JQY
 *   Jiranisoko Tech Solutions   this site, a division, not separately incorporated
 *   JiraniSoko Marketplace      a sibling consumer platform at jiranisoko.com
 *
 * The site chrome previously linked the holding company's name to the marketplace,
 * so someone clicking "Investor & Group" landed on a consumer classifieds app.
 */
class EntitySeparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_site_chrome_never_links_the_group_to_the_marketplace(): void
    {
        $this->seed();

        config(['company.parent.url' => null]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        // Strip the one place the marketplace URL is legitimately allowed to appear.
        $chrome = str_replace(
            $this->get(route('platforms.show', PlatformReference::query()->first()))->getContent(),
            '',
            $html,
        );

        $this->assertStringNotContainsString(
            config('company.marketplace.url'),
            $chrome,
            'The homepage chrome must not link to the marketplace.',
        );
    }

    public function test_the_group_link_resolves_internally_when_no_parent_site_exists(): void
    {
        $this->seed();

        config(['company.parent.url' => null]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Investor &amp; Group', escape: false)
            ->assertSee(route('company.about').'#group', escape: false);
    }

    public function test_the_group_link_uses_the_parent_site_once_one_is_configured(): void
    {
        $this->seed();

        config(['company.parent.url' => 'https://group.example.test']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('https://group.example.test', escape: false);
    }

    public function test_a_group_link_is_never_double_escaped(): void
    {
        $this->seed();

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('&amp;amp;', $html);
    }

    public function test_the_footer_attributes_registration_to_the_holding_company(): void
    {
        $this->seed();

        // The division is not separately incorporated; the number belongs to the parent.
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Registered as')
            ->assertSee('Jiranisoko Market Ltd')
            ->assertSee('PVT-YQ195JQY')
            ->assertSee('Republic of Kenya, Companies Act 2015');
    }

    public function test_the_marketplace_is_reachable_only_through_its_platform_page(): void
    {
        $this->seed();

        $marketplace = PlatformReference::query()->where('slug', 'jiranisoko-marketplace')->sole();

        $this->assertSame('https://jiranisoko.com', $marketplace->external_url);

        $this->get(route('platforms.show', $marketplace))
            ->assertOk()
            ->assertSee('https://jiranisoko.com', escape: false)
            ->assertSee('Entity note')
            ->assertSee('sibling of this division', escape: false);
    }

    public function test_the_operator_claim_does_not_overstate_the_marketplace(): void
    {
        $this->seed();

        // The marketplace is consumer classifieds with M-Pesa rails, not an auction
        // engine. The homepage and the auction service page must not claim otherwise.
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('consumer marketplace')
            ->assertDontSee('commerce and auction platform');

        $this->get('/services/enterprise-software/auction-trading-engines')
            ->assertOk()
            ->assertDontSee('Our group operates an auction platform');
    }
}
