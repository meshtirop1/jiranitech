<?php

namespace Tests\Feature;

use App\Models\Industry;
use App\Models\PlatformReference;
use App\Models\Service;
use App\Support\PublicationGates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Nothing written for us should ever be rendered at a client.
 *
 * The site was built with its publication gates written into the pages
 * themselves, which was useful while it was being assembled and became a
 * liability the moment it was live: a prospective client reading a service page
 * was being told to "set is_published on the job_openings row". The gates still
 * exist and are still enforced — they are reported in the admin console, which
 * is the only audience that can act on them.
 *
 * This crawls every public page and fails if any of that vocabulary comes back.
 */
class NoInternalLanguageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Phrases that only make sense to whoever is building or operating the site.
     *
     * @return array<int, string>
     */
    private function internalPhrases(): array
    {
        return [
            // Distinctive enough to be internal wherever they appear. Generic
            // words like "outstanding" or "before launch" are left out on
            // purpose: they occur in perfectly good client-facing copy, and a
            // test that cries wolf gets muted rather than fixed.
            'Publication gate',
            'Gate G-0',
            'note--gate',
            'not configured',
            'has not been drafted',
            'must be written by counsel',
            'whoever drafts it',
            'as part of this build',
            'is_published',
            'cleared_for_disclosure',
            'job_openings',
            'team_members',
            'posted_at',
            'photo_path',
            'TODO',
            'FIXME',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function publicUrls(): array
    {
        $urls = [];

        foreach (Route::getRoutes() as $route) {
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }

            $uri = $route->uri();

            // Parameterised routes are covered by the page tests that know what
            // to substitute; the console and the delivery system are not public.
            if (str_contains($uri, '{')
                || str_starts_with($uri, 'admin')
                || str_starts_with($uri, '_')
                || in_array($uri, ['sitemap.xml', 'robots.txt'], true)) {
                continue;
            }

            $urls[] = '/'.ltrim($uri, '/');
        }

        return array_unique($urls);
    }

    public function test_no_public_page_speaks_to_the_developer(): void
    {
        $this->seed();

        foreach ($this->publicUrls() as $url) {
            $response = $this->get($url);

            if ($response->status() !== 200) {
                continue;
            }

            $body = $response->getContent();

            foreach ($this->internalPhrases() as $phrase) {
                $this->assertStringNotContainsStringIgnoringCase(
                    $phrase,
                    $body,
                    "{$url} shows a reader the phrase \"{$phrase}\", which is written for us, not for them.",
                );
            }
        }
    }

    public function test_the_interior_pages_that_take_a_parameter_are_clean_too(): void
    {
        $this->seed();

        $service = Service::query()->with('pillar')->firstOrFail();

        $urls = [
            route('services.show', [$service->pillar, $service]),
            route('platforms.show', PlatformReference::query()->firstOrFail()),
            route('industries.show', Industry::query()->firstOrFail()),
        ];

        foreach ($urls as $url) {
            $body = $this->get($url)->assertOk()->getContent();

            foreach ($this->internalPhrases() as $phrase) {
                $this->assertStringNotContainsStringIgnoringCase(
                    $phrase,
                    $body,
                    "{$url} shows a reader the phrase \"{$phrase}\".",
                );
            }
        }
    }

    public function test_the_console_still_reports_the_gates(): void
    {
        // The other half of the bargain: taking the gates off the public pages
        // is only safe because they are still reported somewhere that acts on
        // them. If this ever stops being true, the gates have been deleted
        // rather than relocated.
        $this->seed();

        $gates = PublicationGates::all();

        $this->assertCount(10, $gates);
        $this->assertGreaterThan(0, PublicationGates::openCount());

        foreach ($gates as $gate) {
            $this->assertNotEmpty($gate['detail'], "Gate {$gate['id']} reports nothing.");
        }
    }
}
