<?php

namespace App\Http\Controllers;

use App\Enums\RfpTrack;
use App\Http\Requests\StoreRfpSubmissionRequest;
use App\Mail\RfpAcknowledgement;
use App\Mail\RfpReceived;
use App\Models\Pillar;
use App\Models\RfpSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

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

        $this->notify($submission);

        return redirect()->to(URL::signedRoute('rfp.confirmation', $submission, now()->addHours(6)));
    }

    /**
     * Acknowledge the sender, and tell the proposals desk.
     *
     * Sent inside the request because the host runs no queue worker. A mail
     * failure must not take the submission with it: the enquiry is already saved
     * and the confirmation page already carries the reference, so the worst case
     * is a receipt that did not arrive, logged for someone to notice — not a lost
     * piece of business.
     */
    private function notify(RfpSubmission $submission): void
    {
        try {
            Mail::to($submission->email)->send(new RfpAcknowledgement($submission));
        } catch (Throwable $e) {
            Log::error('RFP acknowledgement failed to send.', [
                'reference' => $submission->reference,
                'error' => $e->getMessage(),
            ]);
        }

        $desk = config('company.email.rfp') ?: config('company.email.enquiries');

        if (blank($desk)) {
            return;
        }

        try {
            Mail::to($desk)->send(new RfpReceived($submission));
        } catch (Throwable $e) {
            Log::error('RFP desk notification failed to send.', [
                'reference' => $submission->reference,
                'error' => $e->getMessage(),
            ]);
        }
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
