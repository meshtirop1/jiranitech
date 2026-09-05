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
}
