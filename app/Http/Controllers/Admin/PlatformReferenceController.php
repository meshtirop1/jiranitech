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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9-]+$/', 'unique:platform_references,slug'],
            'system_context' => ['required', 'string', 'max:4000'],
            'stack' => ['nullable', 'string', 'max:2000'],
            'operating_since' => ['nullable', 'string', 'max:60'],
            'external_url' => ['nullable', 'url', 'max:255'],
            'external_label' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Created undisclosed. Scale figures are published only once written
        // clearance is held, which is a decision and not a checkbox default.
        $platform = PlatformReference::create([
            ...$validated,
            'stack' => collect(preg_split('/\r\n|\r|\n/', (string) ($validated['stack'] ?? '')))
                ->map(fn (string $line) => trim($line))->filter()->values()->all(),
            'scale_metrics' => [],
            'cleared_for_disclosure' => false,
            'sort_order' => $validated['sort_order'] ?? (PlatformReference::max('sort_order') + 1),
        ]);

        return back()->with('status', "{$platform->title} added.");
    }

    public function destroy(PlatformReference $platform): RedirectResponse
    {
        $title = $platform->title;
        $platform->delete();

        return back()->with('status', "{$title} deleted.");
    }
}
