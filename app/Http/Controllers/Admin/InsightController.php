<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InsightFormat;
use App\Http\Controllers\Controller;
use App\Models\Insight;
use App\Models\Pillar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InsightController extends Controller
{
    public function index(): View
    {
        return view('admin.insights.index', [
            'insights' => Insight::query()->with('pillar')->latest('published_at')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.insights.form', [
            'insight' => new Insight,
            'pillars' => Pillar::query()->ordered()->get(),
            'formats' => InsightFormat::cases(),
        ]);
    }

    public function edit(Insight $insight): View
    {
        return view('admin.insights.form', [
            'insight' => $insight,
            'pillars' => Pillar::query()->ordered()->get(),
            'formats' => InsightFormat::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $insight = Insight::create($this->validated($request));

        return redirect()->route('admin.insights.index')->with('status', "“{$insight->title}” created.");
    }

    public function update(Request $request, Insight $insight): RedirectResponse
    {
        $insight->update($this->validated($request, $insight));

        return redirect()->route('admin.insights.index')->with('status', "“{$insight->title}” saved.");
    }

    public function destroy(Insight $insight): RedirectResponse
    {
        $insight->delete();

        return redirect()->route('admin.insights.index')->with('status', 'Insight deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Insight $insight = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('insights', 'slug')->ignore($insight?->id)],
            'format' => ['required', Rule::enum(InsightFormat::class)],
            'pillar_id' => ['nullable', 'exists:pillars,id'],
            'abstract_line' => ['required', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:60000'],
            'read_minutes' => ['required', 'integer', 'min:1', 'max:120'],
            'is_featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['is_featured'] = $request->boolean('is_featured');

        return $validated;
    }
}
