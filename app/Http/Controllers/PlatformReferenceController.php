<?php

namespace App\Http\Controllers;

use App\Models\PlatformReference;
use Illuminate\View\View;

class PlatformReferenceController extends Controller
{
    public function index(): View
    {
        return view('platforms.index', [
            'platforms' => PlatformReference::query()->ordered()->get(),
        ]);
    }

    public function show(PlatformReference $platformReference): View
    {
        return view('platforms.show', [
            'platform' => $platformReference,
        ]);
    }
}
