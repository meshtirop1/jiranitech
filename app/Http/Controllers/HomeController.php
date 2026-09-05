<?php

namespace App\Http\Controllers;

use App\Models\ComplianceClaim;
use App\Models\EngagementModel;
use App\Models\Industry;
use App\Models\Insight;
use App\Models\Metric;
use App\Models\Pillar;
use App\Models\PlatformReference;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Homepage sections H-01 to H-12 per JTS-WEB-IA-001 section 3.
     */
    public function __invoke(): View
    {
        $insights = Insight::query()->published()->latestFirst()->with('pillar')->take(3)->get();

        return view('home', [
            'metrics' => Metric::query()->published()->ordered()->take(4)->get(),
            'claims' => ComplianceClaim::query()->ordered()->get(),
            'platform' => PlatformReference::query()->ordered()->first(),
            'pillars' => Pillar::query()->ordered()->with('services')->get(),
            'engagementModels' => EngagementModel::query()->ordered()->get(),
            'industries' => Industry::query()->ordered()->get(),

            // Gate G-08: the rail suppresses itself below three published items.
            'insights' => $insights->count() >= 3 ? $insights : collect(),
        ]);
    }
}
