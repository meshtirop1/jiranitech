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
}
