<?php

namespace App\Http\Controllers;

use App\Models\Pillar;
use Illuminate\View\View;

class PillarController extends Controller
{
    public function index(): View
    {
        return view('services.index', [
            'pillars' => Pillar::query()->ordered()->with(['services' => fn ($query) => $query->ordered()])->get(),
        ]);
    }

    public function show(Pillar $pillar): View
    {
        $pillar->load(['services' => fn ($query) => $query->ordered()]);

        return view('services.pillar', [
            'pillar' => $pillar,
            'siblings' => Pillar::query()->ordered()->whereKeyNot($pillar->getKey())->get(),
        ]);
    }
}
