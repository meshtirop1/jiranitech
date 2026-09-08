<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Everything waiting on this person to accept or reject.
 *
 * Separate from the dashboard because a lead running several projects needs one
 * queue rather than a list per project, and because a review that has been
 * waiting three days should be visible as such.
 */
class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        return view('erp.reviews', [
            'queue' => $request->user()->reviewQueue(),
        ]);
    }
}
