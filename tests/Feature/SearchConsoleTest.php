<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Proving ownership of the site to a search engine, without tracking anybody.
 *
 * The privacy notice states that this site runs no analytics and sets nothing a
 * third party can read. A verification tag is a different thing — a token in the
 * markup that a console reads once — and these assert it stays that way: a tag
 * when a token is set, nothing at all when it is not, and no script either way.
 */
class SearchConsoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_tag_is_emitted_until_a_token_is_set(): void
    {
        $this->seed();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('google-site-verification', false)
            ->assertDontSee('msvalidate.01', false);
    }

    public function test_a_token_saved_in_the_console_reaches_every_page(): void
    {
        $this->seed();

        Setting::put('company_google_verification', 'abc123token', 'company');
        Setting::flush();
        SiteSettings::applyToConfig();

        foreach ([route('home'), route('services.index'), route('legal.privacy')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('<meta name="google-site-verification" content="abc123token">', false);
        }
    }

    public function test_verification_adds_no_third_party_request(): void
    {
        // The whole point: ownership is proved by a token we serve ourselves, so
        // the claim in the privacy notice survives being verified.
        $this->seed();

        Setting::put('company_google_verification', 'abc123token', 'company');
        Setting::flush();
        SiteSettings::applyToConfig();

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('google-analytics', $html);
        $this->assertStringNotContainsString('googletagmanager', $html);
        $this->assertStringNotContainsString('gtag(', $html);
    }
}
