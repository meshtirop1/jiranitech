<?php

namespace Tests\Feature;

use App\Models\Insight;
use App\Models\JobOpening;
use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * The crawler-facing contract.
 *
 * These assertions exist because every one of them has a silent failure mode: a
 * page with no description gets a snippet Google invents, a canonical built from
 * the request splits a page's signals across every hostname it answers on, and a
 * sitemap listing a gated URL asks a crawler to fetch something we have decided
 * is not ready to be read.
 */
class SeoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<int, string>
     */
    private function publicPages(): array
    {
        return [
            '/',
            '/services',
            '/engagement-models',
            '/industries',
            '/platforms',
            '/insights',
            '/company',
            '/company/about',
            '/company/governance',
            '/company/leadership',
            '/company/delivery-model',
            '/company/careers',
            '/contact',
            '/contact/engagement-desk',
            '/contact/request-for-proposal',
            '/legal/privacy-notice',
            '/legal/terms-of-engagement',
            '/legal/data-processing-addendum',
            '/legal/responsible-disclosure',
            '/legal/accessibility-statement',
        ];
    }

    private function meta(TestResponse $response, string $name): ?string
    {
        preg_match(
            '/<meta\s+name="'.preg_quote($name, '/').'"\s+content="([^"]*)"/i',
            $response->getContent(),
            $matches
        );

        return $matches[1] ?? null;
    }

    private function canonical(TestResponse $response): ?string
    {
        preg_match('/<link\s+rel="canonical"\s+href="([^"]*)"/i', $response->getContent(), $matches);

        return $matches[1] ?? null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    /**
     * The organisation node, whatever shape its @type takes.
     *
     * It carries two types — Organization and ProfessionalService — because both
     * are true and the second is what a local search reads. schema.org allows an
     * array there, so a lookup for the node cannot assume a string.
     *
     * @param  array<int, array<string, mixed>>  $nodes
     * @return array<string, mixed>|null
     */
    private function organisationNode(array $nodes): ?array
    {
        return collect($nodes)->first(
            fn (array $node) => in_array('Organization', (array) ($node['@type'] ?? []), true)
        );
    }

    private function jsonLd(TestResponse $response): array
    {
        preg_match_all(
            '#<script type="application/ld\+json">(.*?)</script>#s',
            $response->getContent(),
            $matches
        );

        $nodes = [];

        foreach ($matches[1] as $block) {
            $decoded = json_decode(html_entity_decode($block), true);

            $this->assertIsArray($decoded, 'A ld+json block did not parse: '.substr($block, 0, 120));
            $this->assertSame('https://schema.org', $decoded['@context'] ?? null);

            foreach ($decoded['@graph'] ?? [] as $node) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

    public function test_every_public_page_carries_a_title_description_and_canonical(): void
    {
        $this->seed();

        foreach ($this->publicPages() as $path) {
            $response = $this->get($path)->assertOk();

            preg_match('#<title>(.*?)</title>#s', $response->getContent(), $title);

            $this->assertNotEmpty($title[1] ?? '', "No <title> on {$path}");
            $this->assertStringContainsString(
                config('company.legal_name'),
                $title[1],
                "Title on {$path} does not name the division."
            );

            $description = $this->meta($response, 'description');

            $this->assertNotNull($description, "No meta description on {$path}");
            // Short enough to survive a SERP without being cut mid-clause, long
            // enough to say something. Google truncates around 155–160.
            $this->assertGreaterThan(60, strlen($description), "Meta description on {$path} is too thin.");

            $this->assertNotNull($this->canonical($response), "No canonical on {$path}");
        }
    }

    public function test_canonicals_are_absolute_and_pinned_to_the_configured_host(): void
    {
        $this->seed();

        foreach ($this->publicPages() as $path) {
            // Arriving on a different host must not change what the page claims
            // to be. This is the split-signal failure the canonical exists to stop.
            $response = $this->get('http://an-old-hostname.test'.$path)->assertOk();

            $canonical = $this->canonical($response);

            $this->assertStringStartsWith(
                rtrim(config('app.url'), '/'),
                (string) $canonical,
                "Canonical on {$path} followed the request host instead of the configured one."
            );
        }
    }

    public function test_public_pages_are_indexable_and_the_console_is_not(): void
    {
        $this->seed();

        foreach ($this->publicPages() as $path) {
            $robots = $this->meta($this->get($path)->assertOk(), 'robots');

            $this->assertNotNull($robots, "No robots meta on {$path}");
            $this->assertStringContainsString('index', $robots);
            $this->assertStringNotContainsString('noindex', $robots, "{$path} is excluded from indexes.");
        }

        // followingRedirects because /admin/login sends you to /admin/setup until
        // an administrator exists, and a 302 carries no markup to inspect.
        foreach (['/admin/setup', '/admin/login'] as $path) {
            $this->assertSame(
                'noindex, nofollow',
                $this->meta($this->followingRedirects()->get($path)->assertOk(), 'robots'),
                "The console page reached from {$path} is indexable."
            );
        }
    }

    public function test_the_signed_confirmation_page_is_never_indexable(): void
    {
        $this->seed();

        $redirect = $this->post(route('rfp.store'), [
            'organisation' => 'Rift Valley Commercial Bank',
            'contact_name' => 'A. Kiprotich',
            'role' => 'Head of Technology',
            'email' => 'procurement@example.com',
            'telephone' => '+254 700 000000',
            'country' => 'Kenya',
            'track' => 'enterprise-modernisation',
            'budget_band' => 'USD 150,000 – 500,000',
            'timeline' => 'This quarter',
            'service_interests' => ['cloud-devops'],
            'scope_summary' => str_repeat('We need a full modernisation of the core ledger. ', 5),
        ]);

        $response = $this->get($redirect->headers->get('Location'))->assertOk();

        $this->assertSame('noindex, nofollow', $this->meta($response, 'robots'));
    }

    public function test_the_sitemap_and_robots_agree_with_the_canonicals_whatever_host_they_are_fetched_on(): void
    {
        $this->seed();

        $site = rtrim(config('app.url'), '/');

        $sitemap = $this->get('http://an-old-hostname.test/sitemap.xml')->assertOk();

        foreach (simplexml_load_string($sitemap->getContent())->url as $entry) {
            $this->assertStringStartsWith(
                $site,
                (string) $entry->loc,
                'A sitemap entry followed the request host, so it contradicts that page\'s canonical.'
            );
        }

        $this->assertStringContainsString(
            'Sitemap: '.$site.'/sitemap.xml',
            $this->get('http://an-old-hostname.test/robots.txt')->assertOk()->getContent()
        );
    }

    public function test_the_sitemap_lists_only_pages_that_answer(): void
    {
        $this->seed();

        $response = $this->get('/sitemap.xml')->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        $xml = simplexml_load_string($response->getContent());

        $this->assertNotFalse($xml, 'The sitemap is not well-formed XML.');
        $this->assertGreaterThan(30, $xml->count(), 'The sitemap looks suspiciously short.');

        foreach ($xml->url as $entry) {
            $loc = (string) $entry->loc;

            $this->assertStringStartsWith(rtrim(config('app.url'), '/'), $loc);
            $this->assertStringNotContainsString('/admin', $loc, 'The console is in the sitemap.');

            $path = parse_url($loc, PHP_URL_PATH) ?: '/';

            $this->assertSame(200, $this->get($path)->getStatusCode(), "Sitemap lists {$path}, which does not answer.");
        }
    }

    public function test_the_sitemap_withholds_an_unpublished_insight(): void
    {
        $this->seed();

        $insight = Insight::query()->published()->firstOrFail();

        $this->assertStringContainsString(
            route('insights.show', $insight),
            $this->get('/sitemap.xml')->getContent()
        );

        $insight->update(['published_at' => null]);

        $this->assertStringNotContainsString(
            route('insights.show', $insight),
            $this->get('/sitemap.xml')->getContent(),
            'A gated insight is being advertised to crawlers.'
        );
    }

    public function test_robots_txt_names_the_sitemap_and_closes_the_console(): void
    {
        $this->seed();

        $response = $this->get('/robots.txt')->assertOk();
        $body = $response->getContent();

        $this->assertStringContainsString('User-agent: *', $body);
        $this->assertStringContainsString('Disallow: /admin', $body);
        $this->assertStringContainsString('Sitemap: '.route('sitemap'), $body);

        // The RFP form itself must stay crawlable; only its signed confirmations
        // are withheld, and the trailing slash is what draws that line.
        $this->assertStringNotContainsString("Disallow: /contact/request-for-proposal\n", $body);
    }

    public function test_every_page_emits_a_parsable_organisation_graph(): void
    {
        $this->seed();

        foreach ($this->publicPages() as $path) {
            $nodes = $this->jsonLd($this->get($path)->assertOk());

            $organisation = $this->organisationNode($nodes);

            $this->assertNotNull($organisation, "No Organization node on {$path}");
            $this->assertSame(config('company.legal_name'), $organisation['name']);
            $this->assertSame(
                config('company.parent.name'),
                $organisation['parentOrganization']['name'],
                'The holding company is misnamed in the graph.'
            );
        }
    }

    public function test_a_deep_page_declares_its_breadcrumb_trail(): void
    {
        $this->seed();

        $nodes = $this->jsonLd($this->get('/company/leadership')->assertOk());
        $trail = collect($nodes)->firstWhere('@type', 'BreadcrumbList');

        $this->assertNotNull($trail, 'No BreadcrumbList on a page that renders crumbs.');
        $this->assertSame('Home', $trail['itemListElement'][0]['name']);
        $this->assertSame(1, $trail['itemListElement'][0]['position']);
        $this->assertSame('Leadership', end($trail['itemListElement'])['name']);
    }

    public function test_the_registered_address_reaches_the_graph_only_once_it_is_set(): void
    {
        $this->seed();

        // The registered office is configured now (it came off the CR12), so this
        // clears it first: what is under test is that an unset address produces no
        // streetAddress rather than a guess, not whether one happens to be set.
        config(['company.registered_address' => null]);

        $before = $this->organisationNode($this->jsonLd($this->get('/')));

        $this->assertArrayNotHasKey(
            'streetAddress',
            $before['address'],
            'An address was published while gate G-07 is still open.'
        );

        Setting::put('company_registered_address', 'Kenyatta Street, Eldoret');
        SiteSettings::applyToConfig();

        $after = $this->organisationNode($this->jsonLd($this->get('/')));

        $this->assertSame('Kenyatta Street, Eldoret', $after['address']['streetAddress']);
    }

    public function test_only_a_published_vacancy_becomes_a_job_posting(): void
    {
        $this->seed();

        $nodes = collect($this->jsonLd($this->get('/company/careers')->assertOk()));

        $this->assertCount(0, $nodes->where('@type', 'JobPosting'), 'A gated vacancy was published to job aggregators.');

        $job = JobOpening::query()->where('slug', 'senior-payments-engineer')->sole();
        $job->update(['is_published' => true, 'posted_at' => now()]);

        $postings = collect($this->jsonLd($this->get('/company/careers')))->where('@type', 'JobPosting');

        $this->assertCount(1, $postings);
        $this->assertSame($job->title, $postings->first()['title']);
        $this->assertNotEmpty($postings->first()['datePosted']);
        $this->assertSame(config('company.legal_name'), $this->organisationName($postings->first()));
    }

    public function test_an_article_page_declares_its_publication_dates(): void
    {
        $this->seed();

        $insight = Insight::query()->published()->firstOrFail();
        $article = collect($this->jsonLd($this->get(route('insights.show', $insight))->assertOk()))
            ->firstWhere('@type', 'TechArticle');

        $this->assertNotNull($article, 'No article markup on an insight.');
        $this->assertSame($insight->title, $article['headline']);
        $this->assertNotEmpty($article['datePublished']);
    }

    public function test_the_brand_marks_the_head_advertises_all_exist(): void
    {
        $this->seed();

        $content = $this->get('/')->assertOk()->getContent();

        preg_match_all('/<link[^>]+rel="(?:icon|apple-touch-icon)"[^>]+href="([^"]+)"/i', $content, $matches);

        $this->assertNotEmpty($matches[1], 'The page advertises no icon at all.');

        foreach (array_unique($matches[1]) as $href) {
            $file = public_path(parse_url($href, PHP_URL_PATH));

            $this->assertFileExists($file, "{$href} is linked but not shipped.");
            // favicon.ico shipped as a zero-byte file once; a link to an empty
            // image is worse than no link, because the browser caches the miss.
            $this->assertGreaterThan(0, filesize($file), "{$href} is an empty file.");
        }
    }

    public function test_the_sharing_card_is_declared_at_the_size_it_actually_is(): void
    {
        $this->seed();

        $response = $this->get('/')->assertOk();

        preg_match('/<meta property="og:image" content="([^"]+)"/', $response->getContent(), $match);

        $this->assertNotEmpty($match[1] ?? '', 'No og:image.');

        $file = public_path(parse_url($match[1], PHP_URL_PATH));

        $this->assertFileExists($file);

        [$width, $height] = getimagesize($file);

        $this->assertSame((int) $this->metaProperty($response, 'og:image:width'), $width);
        $this->assertSame((int) $this->metaProperty($response, 'og:image:height'), $height);
    }

    private function metaProperty(TestResponse $response, string $property): ?string
    {
        preg_match(
            '/<meta property="'.preg_quote($property, '/').'" content="([^"]*)"/i',
            $response->getContent(),
            $matches
        );

        return $matches[1] ?? null;
    }

    /**
     * @param  array<string, mixed>  $posting
     */
    private function organisationName(array $posting): string
    {
        // JobPosting references the organisation by @id; resolve it the way a
        // consumer of the graph would.
        $nodes = collect($this->jsonLd($this->get('/company/careers')));

        return $nodes->firstWhere('@id', $posting['hiringOrganization']['@id'])['name'];
    }
}
