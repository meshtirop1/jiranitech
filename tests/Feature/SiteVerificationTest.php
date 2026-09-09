<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Proving ownership by the HTML file method, without an upload.
 *
 * Google's file method failed here because the file was never put in the
 * document root, and its error says only that it was not found in "the required
 * location". The site answers the address instead, so there is no location to
 * get wrong.
 */
class SiteVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function configure(string $file): void
    {
        Setting::put('company_google_verification_file', $file, 'company');
        Setting::flush();
        SiteSettings::applyToConfig();
    }

    public function test_the_configured_file_returns_exactly_what_google_looks_for(): void
    {
        $this->configure('google1a2b3c4d5e6f.html');

        $this->get('/google1a2b3c4d5e6f.html')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/html; charset=utf-8')
            ->assertSee('google-site-verification: google1a2b3c4d5e6f.html', false);
    }

    public function test_any_other_verification_file_is_a_404(): void
    {
        // Otherwise a stranger could verify ownership of this site in their own
        // console by guessing the shape of the file name, which is public.
        $this->configure('google1a2b3c4d5e6f.html');

        $this->get('/google999999999999.html')->assertNotFound();
    }

    public function test_nothing_is_served_until_a_file_name_is_configured(): void
    {
        $this->get('/google1a2b3c4d5e6f.html')->assertNotFound();
    }

    public function test_the_issued_file_for_this_property_answers_out_of_the_box(): void
    {
        // The name Google actually issued, carried in config so verification does
        // not depend on a settings write. If this ever stops answering, the
        // property silently loses its verification some weeks later.
        $file = config('company.verification.google_file');

        $this->assertNotEmpty($file, 'No verification file name is configured.');

        $this->get('/'.$file)
            ->assertOk()
            ->assertSee("google-site-verification: {$file}", false);
    }

    public function test_the_route_shadows_no_real_page(): void
    {
        // It sits last in the route file and matches only Google's own shape,
        // but the whole site is worth re-checking after adding a root-level
        // catch-all.
        $this->seed();
        $this->configure('google1a2b3c4d5e6f.html');

        foreach (['/', '/services', '/company/governance', '/legal/privacy-notice', '/insights'] as $path) {
            $this->get($path)->assertOk();
        }

        $this->get('/not-a-real-page')->assertNotFound();
    }
}
