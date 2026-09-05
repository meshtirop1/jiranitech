<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RfpSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RfpInboxController extends Controller
{
    public function index(): View
    {
        return view('admin.rfp.index', [
            'submissions' => RfpSubmission::query()->latest()->paginate(25),
        ]);
    }

    public function show(RfpSubmission $rfp): View
    {
        return view('admin.rfp.show', ['submission' => $rfp]);
    }

    public function acknowledge(RfpSubmission $rfp): RedirectResponse
    {
        $rfp->update(['acknowledged_at' => $rfp->acknowledged_at ? null : now()]);

        return back()->with('status', $rfp->acknowledged_at
            ? "{$rfp->reference} marked acknowledged."
            : "{$rfp->reference} marked unacknowledged.");
    }
}
