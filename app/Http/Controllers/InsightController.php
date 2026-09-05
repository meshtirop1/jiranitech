<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use Illuminate\View\View;

class InsightController extends Controller
{
    public function index(): View
    {
        return view('insights.index', [
            'insights' => Insight::query()->published()->latestFirst()->with('pillar')->paginate(12),
        ]);
    }

    public function show(Insight $insight): View
    {
        abort_if($insight->published_at === null || $insight->published_at->isFuture(), 404);

        return view('insights.show', [
            'insight' => $insight->load('pillar'),
            'related' => Insight::query()
                ->published()
                ->latestFirst()
                ->whereKeyNot($insight->getKey())
                ->take(2)
                ->get(),
        ]);
    }
}
