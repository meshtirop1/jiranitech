<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobOpeningController extends Controller
{
    public function index(): View
    {
        return view('admin.jobs.index', [
            'openings' => JobOpening::query()->ordered()->get(),
        ]);
    }

    public function update(Request $request, JobOpening $job): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:160'],
            'arrangement' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:2000'],
            'is_published' => ['boolean'],
        ]);

        $publishing = $request->boolean('is_published');

        $job->update([
            ...$validated,
            'is_published' => $publishing,
            'posted_at' => $publishing ? ($job->posted_at ?? now()) : null,
        ]);

        return back()->with('status', $publishing
            ? "“{$job->title}” is now advertised."
            : "“{$job->title}” withdrawn from the careers page.");
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9-]+$/', 'unique:job_openings,slug'],
            'level' => ['required', 'string', 'max:120'],
            'discipline' => ['nullable', 'string', 'max:160'],
            'location' => ['required', 'string', 'max:160'],
            'arrangement' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:2000'],
            'responsibilities' => ['nullable', 'string', 'max:4000'],
            'requirements' => ['nullable', 'string', 'max:4000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Created unadvertised on purpose. Publishing is a separate, deliberate
        // act, because advertising a role that is not open and funded wastes a
        // candidate's time.
        $job = JobOpening::create([
            ...$validated,
            'responsibilities' => $this->lines($validated['responsibilities'] ?? null),
            'requirements' => $this->lines($validated['requirements'] ?? null),
            'is_published' => false,
            'posted_at' => null,
            'sort_order' => $validated['sort_order'] ?? (JobOpening::max('sort_order') + 1),
        ]);

        return back()->with('status', "\u{201C}{$job->title}\u{201D} created, and held back until you publish it.");
    }

    public function destroy(JobOpening $job): RedirectResponse
    {
        $title = $job->title;
        $job->delete();

        return back()->with('status', "\u{201C}{$title}\u{201D} deleted.");
    }

    /**
     * @return array<int, string>
     */
    private function lines(?string $input): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $input))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
