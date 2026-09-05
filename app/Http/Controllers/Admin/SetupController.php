<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * First-run administrator creation.
 *
 * The host has no shell, so there is no way to run a console command to seed the first
 * account. This route creates it from a browser and then closes permanently: once any
 * administrator exists, every action here 404s. That keeps the window open for exactly
 * one use rather than leaving a standing registration endpoint on a public site.
 */
class SetupController extends Controller
{
    public function create(): View
    {
        $this->abortIfAlreadySetUp();

        return view('admin.setup');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->abortIfAlreadySetUp();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()->symbols()],
        ], [
            'password.confirmed' => 'The two passwords do not match.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('status', 'Administrator created. This setup page is now closed.');
    }

    private function abortIfAlreadySetUp(): void
    {
        abort_if(User::query()->where('is_admin', true)->exists(), 404);
    }
}
