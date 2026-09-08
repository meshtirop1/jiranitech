<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamMemberController extends Controller
{
    public function index(): View
    {
        return view('admin.team.index', [
            'members' => TeamMember::query()->ordered()->get(),
        ]);
    }

    public function update(Request $request, TeamMember $team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'role_title' => ['required', 'string', 'max:255'],
            'accountability' => ['required', 'string', 'max:2000'],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
        ]);

        $team->update([...$validated, 'is_published' => $request->boolean('is_published')]);

        return back()->with('status', "“{$team->role_title}” saved.");
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'role_title' => ['required', 'string', 'max:255'],
            'discipline' => ['nullable', 'string', 'max:255'],
            'accountability' => ['required', 'string', 'max:2000'],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $member = TeamMember::create([
            ...$validated,
            'remit' => [],
            'is_published' => $request->boolean('is_published'),
            'sort_order' => $validated['sort_order'] ?? (TeamMember::max('sort_order') + 1),
        ]);

        return back()->with('status', "The post \u{201C}{$member->role_title}\u{201D} was added.");
    }

    public function destroy(TeamMember $team): RedirectResponse
    {
        $title = $team->role_title;
        $team->delete();

        return back()->with('status', "The post \u{201C}{$title}\u{201D} was removed.");
    }
}
