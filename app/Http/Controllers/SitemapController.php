<?php

namespace App\Http\Controllers;

use App\Models\EngagementModel;
use App\Models\Industry;
use App\Models\Insight;
use App\Models\Pillar;
use App\Models\PlatformReference;
use App\Support\StructuredData;
use Illuminate\Http\Response;

/**
 * XML sitemap.
 *
 * Generated rather than written, because a hand-kept sitemap drifts the first time
 * a service is added and then quietly advertises URLs that 404. Everything listed
 * here is reachable and indexable: the console is excluded, and so is anything a
 * publication gate is still holding back — an insight with no publication date
 * returns 404 from its own controller, and listing it would ask a crawler to fetch
 * a page we have decided is not ready.
 */
class SitemapController extends Controller
{
    /**
     * changefreq and priority are hints, not instructions, and search engines
     * largely ignore them. They are set here to describe the site honestly:
     * the homepage and the capability taxonomy move; the legal pages do not.
     */
    public function __invoke(): Response
    {
        $urls = [];

        // Built from the configured site URL rather than the request, for the same
        // reason the canonicals are. A sitemap fetched over http, or on the bare
        // server name, would otherwise list a set of URLs that contradicts the
        // canonical on every page it points at.
        $add = function (string $name, array $parameters, ?string $lastmod, string $changefreq, string $priority) use (&$urls): void {
            $urls[] = [
                'url' => StructuredData::canonical(route($name, $parameters, absolute: false)),
                'lastmod' => $lastmod,
                'changefreq' => $changefreq,
                'priority' => $priority,
            ];
        };

        $add('home', [], null, 'weekly', '1.0');

        // Capability taxonomy — the commercial core of the site.
        $add('services.index', [], null, 'monthly', '0.9');

        foreach (Pillar::query()->ordered()->with('services')->get() as $pillar) {
            $add('pillars.show', [$pillar], $pillar->updated_at?->toAtomString(), 'monthly', '0.8');

            foreach ($pillar->services as $service) {
                $add(
                    'services.show',
                    [$pillar, $service],
                    $service->updated_at?->toAtomString(),
                    'monthly',
                    '0.7'
                );
            }
        }

        $add('engagement-models.index', [], null, 'monthly', '0.8');
        foreach (EngagementModel::query()->ordered()->get() as $model) {
            $add('engagement-models.show', [$model], $model->updated_at?->toAtomString(), 'yearly', '0.6');
        }

        $add('industries.index', [], null, 'monthly', '0.7');
        foreach (Industry::query()->ordered()->get() as $industry) {
            $add('industries.show', [$industry], $industry->updated_at?->toAtomString(), 'yearly', '0.6');
        }

        $add('platforms.index', [], null, 'monthly', '0.7');
        foreach (PlatformReference::query()->ordered()->get() as $platform) {
            $add('platforms.show', [$platform], $platform->updated_at?->toAtomString(), 'yearly', '0.6');
        }

        // Published insights only. Insight::show() aborts 404 on the rest.
        $add('insights.index', [], null, 'weekly', '0.7');
        foreach (Insight::query()->published()->latestFirst()->get() as $insight) {
            $add('insights.show', [$insight], $insight->updated_at?->toAtomString(), 'yearly', '0.6');
        }

        foreach ([
            'company.index' => '0.7',
            'company.about' => '0.6',
            'company.governance' => '0.6',
            'company.leadership' => '0.6',
            'company.delivery-model' => '0.6',
            'company.careers' => '0.7',
        ] as $name => $priority) {
            $add($name, [], null, 'monthly', $priority);
        }

        $add('contact.index', [], null, 'yearly', '0.8');
        $add('contact.engagement-desk', [], null, 'yearly', '0.7');
        $add('rfp.create', [], null, 'yearly', '0.9');

        foreach ([
            'legal.privacy',
            'legal.terms',
            'legal.data-processing',
            'legal.disclosure',
            'legal.accessibility',
        ] as $name) {
            $add($name, [], null, 'yearly', '0.3');
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
