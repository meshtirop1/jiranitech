<?php

namespace App\Http\Controllers\Erp;

use App\Erp\Support\DemoData;
use App\Erp\Support\FoundingPost;
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
 * What keeps that safe is when it is reachable. Planting is offered only to
 * somebody holding the founding post, which by definition means the division is
 * empty and the account is the site administrator standing in. The moment the
 * example is planted it has a Managing Director in it, the founding post lapses,
 * and this route refuses everyone — including the account that just used it.
 * It cannot be called twice and cannot be called on a division doing real work.
 */
class DemoDataController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless(
            FoundingPost::heldBy($request->user()),
            403,
            'The worked example can only be loaded into an empty division.',
        );

        abort_if(DemoData::exists(), 409, 'The worked example is already loaded.');

        DemoData::plant();

        // Planting appoints a Managing Director, so the founding post has just
        // lapsed and this session no longer reaches delivery. Send them to the
        // sign-in page with the credentials rather than into a refusal.
        return redirect()->route('erp.login')->with('status', sprintf(
            'Worked example loaded. Sign in as md@%s — every demonstration account uses the '
            .'password %s. The full roll is on the People page once you are in.',
            DemoData::DOMAIN,
            DemoData::PASSWORD,
        ));
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
