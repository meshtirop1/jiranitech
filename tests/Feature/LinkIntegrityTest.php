<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Crawls every reachable page and asserts that no link is left unattended:
 * every internal href resolves, no placeholder "#" or empty href ships, and no
 * mailto or tel is rendered with an empty address behind it.
 */
class LinkIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Static GET routes. Parameterised routes are crawled from the pages that link
     * to them, which is also what proves those links are correct.
     */
    private function seedPages(): array
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

    /**
     * @return array<int, string>
     */
    private function hrefsFrom(TestResponse $response): array
    {
        preg_match_all('/<a\b[^>]*href="([^"]*)"/i', $response->getContent(), $matches);

        return $matches[1];
    }

    public function test_no_page_ships_a_placeholder_or_empty_href(): void
    {
        $this->seed();

        foreach ($this->seedPages() as $path) {
            $response = $this->get($path)->assertOk();

            foreach ($this->hrefsFrom($response) as $href) {
                $this->assertNotSame('', trim($href), "Empty href on {$path}");
                $this->assertNotSame('#', trim($href), "Placeholder '#' href on {$path}");
                $this->assertStringNotContainsString(
                    'mailto:"',
                    $href,
                    "Empty mailto on {$path}",
                );
                $this->assertDoesNotMatchRegularExpression(
                    '/^(mailto:|tel:)\s*$/',
                    $href,
                    "Empty {$href} on {$path}",
                );
            }
        }
    }

    public function test_every_internal_link_resolves(): void
    {
        $this->seed();

        $checked = [];
        $failures = [];

        foreach ($this->seedPages() as $path) {
            $response = $this->get($path)->assertOk();

            foreach ($this->hrefsFrom($response) as $href) {
                $target = $this->normaliseInternal($href);

                if ($target === null || isset($checked[$target])) {
                    continue;
                }

                $checked[$target] = true;
                $status = $this->get($target)->getStatusCode();

                if ($status !== 200) {
                    $failures[] = "{$target} -> {$status} (linked from {$path})";
                }
            }
        }

        $this->assertSame([], $failures, "Broken internal links:\n".implode("\n", $failures));
        $this->assertGreaterThan(40, count($checked), 'Crawl found suspiciously few internal links.');
    }

    public function test_in_page_anchors_point_at_an_element_that_exists(): void
    {
        $this->seed();

        foreach ($this->seedPages() as $path) {
            $content = $this->get($path)->assertOk()->getContent();

            preg_match_all('/<a\b[^>]*href="#([A-Za-z][\w:.-]*)"/i', $content, $matches);

            foreach (array_unique($matches[1]) as $fragment) {
                $this->assertMatchesRegularExpression(
                    '/\bid="'.preg_quote($fragment, '/').'"/',
                    $content,
                    "Anchor #{$fragment} on {$path} has no matching element id.",
                );
            }
        }
    }

    public function test_every_named_route_that_takes_no_parameters_responds(): void
    {
        $this->seed();

        foreach (Route::getRoutes() as $route) {
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }

            if ($route->parameterNames() !== [] || $route->getName() === null) {
                continue;
            }

            if (array_intersect(['signed', 'auth'], $route->gatherMiddleware()) !== []) {
                continue;
            }

            // The console is not part of the public site. Its own routes redirect by
            // design — /admin/login sends you to /admin/setup until an administrator
            // exists — and AdminConsoleTest covers them properly.
            if (str_starts_with((string) $route->getName(), 'admin.')) {
                continue;
            }

            $this->get('/'.ltrim($route->uri(), '/'))
                ->assertOk("Named route {$route->getName()} did not respond.");
        }
    }

    /**
     * Returns the app-relative path for an internal link, or null for anything
     * pointing off-site or at a non-HTTP scheme.
     */
    private function normaliseInternal(string $href): ?string
    {
        $href = trim($href);

        if ($href === '' || str_starts_with($href, '#')) {
            return null;
        }

        if (preg_match('/^(mailto:|tel:|javascript:|data:)/i', $href)) {
            return null;
        }

        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $host = parse_url($href, PHP_URL_HOST);

        if ($host !== null && $host !== $appHost) {
            return null;
        }

        $path = parse_url($href, PHP_URL_PATH) ?: '/';

        return $path;
    }
}
