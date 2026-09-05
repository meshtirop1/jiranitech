<?php

namespace App\Http\Controllers;

use App\Enums\RfpTrack;
use App\Http\Requests\StoreRfpSubmissionRequest;
use App\Models\Pillar;
use App\Models\RfpSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Template T-14, the structured RFP intake.
 */
class RfpSubmissionController extends Controller
{
    public function create(Request $request): View
    {
        return view('rfp.create', [
            'tracks' => RfpTrack::cases(),
            'selectedTrack' => RfpTrack::tryFrom((string) $request->query('track')),
            'pillars' => Pillar::query()->ordered()->get(),
            'budgetBands' => StoreRfpSubmissionRequest::BUDGET_BANDS,
            'timelines' => StoreRfpSubmissionRequest::TIMELINES,
        ]);
    }

    public function store(StoreRfpSubmissionRequest $request): RedirectResponse
    {
        $submission = RfpSubmission::create([
            ...$request->validated(),
            'reference' => $this->nextReference(),
            'submitted_ip' => $request->ip(),
            'source_page' => $request->input('source_page'),
        ]);

        return redirect()->to(URL::signedRoute('rfp.confirmation', $submission, now()->addHours(6)));
    }

    public function show(RfpSubmission $rfpSubmission): View
    {
        return view('rfp.confirmation', [
            'submission' => $rfpSubmission,
        ]);
    }

    /**
     * Human-quotable reference an enquirer can cite in correspondence.
     */
    private function nextReference(): string
    {
        return 'JTS-RFP-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
    }
}
