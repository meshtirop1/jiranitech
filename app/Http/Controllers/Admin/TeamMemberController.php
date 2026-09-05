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
}
