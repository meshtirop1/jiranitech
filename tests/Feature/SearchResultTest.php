<?php

namespace Tests\Feature;

use App\Models\Pillar;
use App\Models\Service;
use App\Support\Seo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * What a search result actually shows.
 *
 * Twenty-seven pages carried titles between 61 and 88 characters, and sixteen
 * carried descriptions between 162 and 303. None of that is a penalty; it is
 * worse than a penalty, because the page ranks and then presents a sentence that
 * stops in the middle to whoever is deciding which result to click.
 *
 * These crawl the sitemap rather than checking a sample, since the failure was
 * systematic — one line in the layout, applied to every page.
 */
class SearchResultTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<int, string> */
    private function indexablePaths(): array
    {
        $paths = [];

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();

            if (! in_array('GET', $route->methods(), true)
                || str_contains($uri, '{')
                || str_starts_with($uri, 'admin')
                || in_array($uri, ['sitemap.xml', 'robots.txt', 'up'], true)) {
                continue;
            }

            $paths[] = '/'.ltrim($uri, '/');
        }

        // The deep templates are where the problem was, so they are covered
        // explicitly rather than left to the parameterless routes.
        $service = Service::query()->with('pillar')->first();

        if ($service) {
            $paths[] = route('services.show', [$service->pillar, $service], absolute: false);
            $paths[] = route('pillars.show', $service->pillar, absolute: false);
        }

        return array_values(array_unique($paths));
    }

    /** @return array{title: string, description: ?string} */
    private function metaOf(string $path): array
    {
        $html = $this->get($path)->getContent();

        preg_match('/<title>(.*?)<\/title>/s', $html, $t);
        preg_match('/<meta name="description" content="([^"]*)"/', $html, $d);

        return [
            'title' => html_entity_decode(trim($t[1] ?? ''), ENT_QUOTES),
            'description' => isset($d[1]) ? html_entity_decode($d[1], ENT_QUOTES) : null,
        ];
    }

    public function test_no_title_is_cut_off_in_a_search_result(): void
    {
        $this->seed();

        foreach ($this->indexablePaths() as $path) {
            if ($this->get($path)->status() !== 200) {
                continue;
            }

            $title = $this->metaOf($path)['title'];

            $this->assertLessThanOrEqual(
                Seo::TITLE_MAX,
                mb_strlen($title),
                "{$path} has a ".mb_strlen($title)." character title: \"{$title}\"",
            );
        }
    }

    public function test_no_description_is_cut_off_in_a_search_result(): void
    {
        $this->seed();

        foreach ($this->indexablePaths() as $path) {
            if ($this->get($path)->status() !== 200) {
                continue;
            }

            $description = $this->metaOf($path)['description'];

            if ($description === null) {
                continue;
            }

            $this->assertLessThanOrEqual(
                Seo::DESCRIPTION_MAX,
                mb_strlen($description),
                "{$path} has a ".mb_strlen($description).' character description.',
            );
        }
    }

    public function test_every_indexable_page_carries_a_description(): void
    {
        $this->seed();

        foreach ($this->indexablePaths() as $path) {
            $response = $this->get($path);

            if ($response->status() !== 200 || str_contains($response->getContent(), 'noindex')) {
                continue;
            }

            $this->assertNotNull(
                $this->metaOf($path)['description'],
                "{$path} is indexable but offers a crawler no description.",
            );
        }
    }

    // --- how the two are composed --------------------------------------------

    public function test_the_brand_is_kept_when_it_fits_and_dropped_when_it_does_not(): void
    {
        $short = Seo::title('Governance');
        $long = Seo::title('Payment Gateway Integration & Custom Gateway Development');

        $this->assertStringContainsString('Jiranisoko', $short);
        $this->assertTrue(Seo::fits($short));

        // The page title alone is 56 characters; there is no room for a suffix
        // and the end of the phrase is worth more than the brand.
        $this->assertSame('Payment Gateway Integration & Custom Gateway Development', $long);
        $this->assertTrue(Seo::fits($long));
    }

    public function test_a_middling_title_keeps_the_short_brand(): void
    {
        // Long enough that the full name will not fit, short enough that
        // "Jiranisoko Tech" will. Dropping the brand entirely would be a waste.
        $title = Seo::title('Cloud Strategy and Architecture Review');

        $this->assertStringContainsString('Jiranisoko Tech', $title);
        $this->assertTrue(Seo::fits($title));
    }

    public function test_a_description_ends_on_a_sentence_rather_than_mid_word(): void
    {
        $text = 'Payment gateway integration across mobile money, card and bank rails. '
            .'Idempotent callback handling, reconciliation and a settlement ledger that '
            .'survives an audit years after the engagement closes.';

        $trimmed = Seo::description($text);

        $this->assertLessThanOrEqual(Seo::DESCRIPTION_MAX, mb_strlen($trimmed));
        $this->assertStringEndsWith('.', $trimmed);
        $this->assertStringNotContainsString('…', $trimmed);
    }

    public function test_a_description_with_no_sentence_break_ends_on_a_word(): void
    {
        $trimmed = Seo::description(str_repeat('infrastructure ', 30));

        $this->assertLessThanOrEqual(Seo::DESCRIPTION_MAX, mb_strlen($trimmed));
        $this->assertStringEndsWith('…', $trimmed);
        $this->assertStringNotContainsString('infrastruct…', $trimmed);
    }

    public function test_a_short_description_is_left_exactly_as_written(): void
    {
        $written = 'How we run delivery, and what a client sees each week.';

        $this->assertSame($written, Seo::description($written));
    }

    // --- structured data ------------------------------------------------------

    public function test_a_service_page_says_what_service_it_is_selling(): void
    {
        $this->seed();
        $service = Service::query()->with('pillar')->firstOrFail();

        $html = $this->get(route('services.show', [$service->pillar, $service]))->assertOk()->getContent();

        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $m);
        $nodes = collect($m[1])->flatMap(fn ($json) => json_decode($json, true)['@graph'] ?? []);

        $node = $nodes->firstWhere('@type', 'Service');

        $this->assertNotNull($node, 'A service page carries no Service node.');
        $this->assertSame($service->title, $node['name']);
        $this->assertSame($service->pillar->title, $node['category']);
        $this->assertArrayHasKey('provider', $node);
        $this->assertNotEmpty($node['areaServed']);
    }

    public function test_every_page_names_the_site_and_where_it_operates(): void
    {
        $this->seed();

        $html = $this->get(route('home'))->assertOk()->getContent();

        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $m);
        $nodes = collect(json_decode($m[1], true)['@graph']);

        $website = $nodes->firstWhere('@type', 'WebSite');
        $this->assertNotNull($website, 'No WebSite node.');
        $this->assertSame('en-KE', $website['inLanguage']);

        $org = $nodes->first(fn ($n) => in_array('Organization', (array) $n['@type'], true));
        $this->assertContains('ProfessionalService', (array) $org['@type']);
        $this->assertNotEmpty($org['areaServed']);
    }

    public function test_a_pillar_still_resolves_after_all_of_this(): void
    {
        $this->seed();

        $this->get(route('pillars.show', Pillar::query()->firstOrFail()))->assertOk();
    }
}
