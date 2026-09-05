<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\Pillar;
use Illuminate\View\View;

class IndustryController extends Controller
{
    public function index(): View
    {
        return view('industries.index', [
            'industries' => Industry::query()->ordered()->get(),
        ]);
    }

    public function show(Industry $industry): View
    {
        return view('industries.show', [
            'industry' => $industry,
            'pillars' => Pillar::query()->ordered()->get(),
        ]);
    }
}
