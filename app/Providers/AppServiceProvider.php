<?php

namespace App\Providers;

use App\Models\EngagementModel;
use App\Models\Industry;
use App\Models\Pillar;
use App\Models\PlatformReference;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        SiteSettings::applyToConfig();

        $this->composeSiteNavigation();
    }

    /**
     * The header megamenu and the footer both need the full taxonomy on every page.
     * A composer keeps that out of eleven controllers.
     */
    private function composeSiteNavigation(): void
    {
        View::composer(
            ['components.site.header', 'components.site.footer'],
            function (ViewInstance $view): void {
                $view->with([
                    'navPillars' => Pillar::query()->ordered()->with(['services' => fn ($query) => $query->ordered()])->get(),
                    'navEngagementModels' => EngagementModel::query()->ordered()->get(),
                    'navIndustries' => Industry::query()->ordered()->get(),
                    'navPlatforms' => PlatformReference::query()->ordered()->get(),
                ]);
            },
        );
    }
}
