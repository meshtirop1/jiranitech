<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformReference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformReferenceController extends Controller
{
    public function index(): View
    {
        return view('admin.platforms.index', [
            'platforms' => PlatformReference::query()->ordered()->get(),
        ]);
    }

    public function update(Request $request, PlatformReference $platform): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'system_context' => ['required', 'string', 'max:4000'],
            'external_url' => ['nullable', 'url', 'max:255'],
            'external_label' => ['nullable', 'string', 'max:120'],
            'operating_since' => ['nullable', 'string', 'max:60'],
            'scale_metrics' => ['nullable', 'array'],
            'scale_metrics.*.label' => ['required_with:scale_metrics', 'string', 'max:160'],
            'scale_metrics.*.value' => ['required_with:scale_metrics', 'string', 'max:160'],
            'cleared_for_disclosure' => ['boolean'],
        ]);

        $platform->update([
            ...$validated,
            'scale_metrics' => array_values($validated['scale_metrics'] ?? []),
            'cleared_for_disclosure' => $request->boolean('cleared_for_disclosure'),
        ]);

        return back()->with('status', "{$platform->title} updated.");
    }
}
