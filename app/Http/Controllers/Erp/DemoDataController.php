<?php

namespace App\Http\Controllers\Erp;

use App\Erp\Models\Project;
use App\Erp\Support\DemoData;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Loading and clearing the worked example.
 *
 * This exists because the host has no shell: a seeder that can only be run from
 * a command line is a seeder that can never be run here. Putting it behind a
 * button is not a convenience, it is the only route there is.
 *
 * What keeps that safe is when it is reachable: only into a delivery system that
 * holds no engagements at all, and only for somebody who could open one. The
 * example plants three, so the door shuts behind it and cannot be opened again
 * until they are cleared. There is no state in which this can touch real work,
 * because real work means there is an engagement, and an engagement means this
 * refuses.
 */
class DemoDataController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $actor = $request->user();

        abort_unless(
            (bool) $actor->erpRole()?->opensProjects(),
            403,
            'Loading the worked example is reserved to the posts that may open an engagement.',
        );

        abort_if(
            Project::query()->exists(),
            409,
            'The worked example only goes into a delivery system with no engagements in it.',
        );

        DemoData::plant();

        $credentials = sprintf(
            'Worked example loaded: three engagements and thirteen accounts. Sign in as md@%s — '
            .'every demonstration account takes the password %s.',
            DemoData::DOMAIN,
            DemoData::PASSWORD,
        );

        // An administrator who was only standing in has just handed the division
        // over to the Managing Director this planted, so they no longer reach
        // delivery. Send them to the sign-in page rather than into a refusal.
        return $actor->fresh()->worksInDelivery()
            ? redirect()->route('erp.people.index')->with('status', $credentials)
            : redirect()->route('erp.login')->with('status', $credentials);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $actor = $request->user();

        abort_unless(
            (bool) $actor->erpRole()?->opensProjects(),
            403,
            'Clearing the worked example is reserved to the Managing Director, the Chief '
            .'Technology Officer and the Director of Delivery.',
        );

        abort_unless(DemoData::exists(), 404, 'There is no worked example to clear.');

        DemoData::remove();

        // Whoever cleared it was most likely one of the accounts it created.
        if ($actor->fresh() === null) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('erp.login')
                ->with('status', 'Worked example cleared, including the account you were using. '
                    .'Sign in with your administrator account to enrol the real team.');
        }

        return redirect()->route('erp.dashboard')
            ->with('status', 'Worked example cleared. What is left is real work only.');
    }
}
