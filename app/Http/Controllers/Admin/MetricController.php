<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Metric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetricController extends Controller
{
    public function index(): View
    {
        return view('admin.metrics.index', [
            'metrics' => Metric::query()->ordered()->get(),
        ]);
    }

    public function update(Request $request, Metric $metric): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:255'],
            'basis' => ['required', 'string', 'max:2000'],
            'substantiation_ref' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
        ], [
            'basis.required' => 'A metric cannot be saved without stating what it rests on. That is the whole point of gate G-01.',
        ]);

        $metric->update([...$validated, 'is_published' => $request->boolean('is_published')]);

        return back()->with('status', "Metric “{$metric->label}” saved.");
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9_]+$/', 'unique:metrics,key'],
            'label' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:255'],
            'basis' => ['required', 'string', 'max:2000'],
            'effective_on' => ['required', 'date'],
            'substantiation_ref' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [
            'basis.required' => 'A metric cannot be created without stating what it rests on.',
        ]);

        // Created unpublished. A figure goes on the site as a separate decision,
        // once somebody has read the basis it carries.
        $metric = Metric::create([
            ...$validated,
            'is_published' => false,
            'sort_order' => $validated['sort_order'] ?? (Metric::max('sort_order') + 1),
        ]);

        return back()->with('status', "Metric \u{201C}{$metric->label}\u{201D} created, unpublished.");
    }

    public function destroy(Metric $metric): RedirectResponse
    {
        $label = $metric->label;
        $metric->delete();

        return back()->with('status', "Metric \u{201C}{$label}\u{201D} deleted.");
    }
}
