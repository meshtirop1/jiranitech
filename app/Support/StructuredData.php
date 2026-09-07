<?php

namespace App\Support;

use App\Models\Insight;
use App\Models\JobOpening;
use Illuminate\Support\Collection;

/**
 * schema.org graphs for the public site.
 *
 * The same rule that governs the visible copy governs the markup: nothing is
 * asserted that has not been substantiated. A registered address that is still
 * behind gate G-07 is omitted rather than guessed at, and an unpublished vacancy
 * produces no JobPosting, because structured data is read by machines that cannot
 * tell a placeholder from a fact and republish it as though we had.
 */
class StructuredData
{
    /**
     * The site's own origin, taken from configuration rather than from the
     * request. Everything a crawler stores — canonicals, @id values, sitemap
     * entries — has to name one host, or the same page accumulates several
     * identities and the signals divide between them.
     */
    public static function siteUrl(): string
    {
        return rtrim(config('app.url'), '/');
    }

    /**
     * Absolute canonical URL for a request path.
     */
    public static function canonical(string $path): string
    {
        $path = trim($path, '/');

        return $path === '' ? self::siteUrl() : self::siteUrl().'/'.$path;
    }

    /**
     * The publisher, referenced by @id from every other node so the graph
     * describes one organisation rather than repeating it per page.
     */
    public static function organisationId(): string
    {
        return self::siteUrl().'/#organisation';
    }

    /**
     * @return array<string, mixed>
     */
    public static function organisation(): array
    {
        $node = [
            '@type' => 'Organization',
            '@id' => self::organisationId(),
            'name' => config('company.legal_name'),
            'url' => self::siteUrl().'/',
            'description' => 'Enterprise technology division delivering artificial intelligence, '
                .'cloud infrastructure and transaction-grade software for financial institutions, '
                .'public-sector agencies and high-growth enterprises.',
            'parentOrganization' => array_filter([
                '@type' => 'Organization',
                'name' => config('company.parent.name'),
                'identifier' => config('company.parent.registration_number'),
                'url' => config('company.parent.url'),
            ]),
        ];

        if (filled($logo = self::socialImage())) {
            $node['logo'] = $logo;
            $node['image'] = $logo;
        }

        // Gate G-07. An address is a legal representation; publish it only once
        // it has been taken from the CR12 and entered in the console.
        if (filled($address = config('company.registered_address'))) {
            $node['address'] = array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
                'addressLocality' => config('company.city'),
                'addressCountry' => config('company.country'),
            ]);
        } else {
            $node['address'] = array_filter([
                '@type' => 'PostalAddress',
                'addressLocality' => config('company.city'),
                'addressCountry' => config('company.country'),
            ]);
        }

        $contact = array_filter([
            '@type' => 'ContactPoint',
            'contactType' => 'sales',
            'email' => config('company.email.rfp') ?: config('company.email.enquiries'),
            'telephone' => config('company.telephone'),
            'areaServed' => 'KE',
            'availableLanguage' => 'en',
        ]);

        // A contact point with neither an address nor a number tells a crawler nothing.
        if (isset($contact['email']) || isset($contact['telephone'])) {
            $node['contactPoint'] = [$contact];
        }

        return $node;
    }

    /**
     * @param  array<string, string|null>  $crumbs  label => url, terminal item null
     * @return array<string, mixed>|null
     */
    public static function breadcrumbs(array $crumbs): ?array
    {
        if ($crumbs === []) {
            return null;
        }

        $items = [[
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => self::siteUrl().'/',
        ]];

        $position = 1;

        foreach ($crumbs as $label => $url) {
            $position++;
            $items[] = array_filter([
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $label,
                'item' => $url,
            ], fn ($value) => $value !== null);
        }

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function article(Insight $insight): array
    {
        return array_filter([
            '@type' => 'TechArticle',
            'headline' => $insight->title,
            'description' => $insight->abstract_line,
            'url' => route('insights.show', $insight),
            'datePublished' => $insight->published_at?->toAtomString(),
            'dateModified' => $insight->updated_at?->toAtomString(),
            'inLanguage' => 'en',
            'publisher' => ['@id' => self::organisationId()],
            // The house does not by-line its analysis to individuals; the division
            // is the author of record, which is also what the page states.
            'author' => ['@id' => self::organisationId()],
        ]);
    }

    /**
     * Google requires a JobPosting to carry a title, a description, a posting date
     * and a hiring organisation. A listing behind gate G-10 has no posted_at, and
     * emitting one anyway would put a vacancy into job aggregators that nobody has
     * agreed to fill.
     *
     * @param  Collection<int, JobOpening>  $jobs
     * @return array<int, array<string, mixed>>
     */
    public static function jobPostings(Collection $jobs): array
    {
        return $jobs
            ->filter(fn (JobOpening $job) => $job->is_published && $job->posted_at !== null)
            ->map(fn (JobOpening $job) => array_filter([
                '@type' => 'JobPosting',
                'title' => $job->title,
                'description' => $job->summary,
                'datePosted' => $job->posted_at?->toDateString(),
                'employmentType' => self::employmentType($job),
                'hiringOrganization' => ['@id' => self::organisationId()],
                'jobLocationType' => str_contains(strtolower((string) $job->arrangement), 'remote')
                    ? 'TELECOMMUTE'
                    : null,
                'jobLocation' => [
                    '@type' => 'Place',
                    'address' => array_filter([
                        '@type' => 'PostalAddress',
                        'addressLocality' => $job->location ?: config('company.city'),
                        'addressCountry' => config('company.country'),
                    ]),
                ],
                'directApply' => filled(config('company.email.enquiries')),
            ]))
            ->values()
            ->all();
    }

    private static function employmentType(JobOpening $job): ?string
    {
        $arrangement = strtolower((string) $job->arrangement);

        return match (true) {
            str_contains($arrangement, 'part') => 'PART_TIME',
            str_contains($arrangement, 'contract') => 'CONTRACTOR',
            str_contains($arrangement, 'intern') => 'INTERN',
            $arrangement === '' => null,
            default => 'FULL_TIME',
        };
    }

    /**
     * Absolute URL of the sharing card, or null while the file is absent so that
     * no page advertises an image that resolves to a 404.
     */
    public static function socialImage(): ?string
    {
        return file_exists(public_path('social-card.png'))
            ? self::siteUrl().'/social-card.png'
            : null;
    }

    /**
     * Wraps the page's nodes in a single @graph so one script tag carries
     * everything and the nodes can cross-reference by @id.
     *
     * @param  array<int, array<string, mixed>|null>  $nodes
     */
    public static function graph(array $nodes): string
    {
        $nodes = array_values(array_filter($nodes));

        return json_encode([
            '@context' => 'https://schema.org',
            '@graph' => $nodes,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
