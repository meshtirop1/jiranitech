<?php

namespace App\Mail;

use App\Models\RfpSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to whoever submitted the RFP form.
 *
 * The site promises an acknowledgement in its own copy, so this is a commitment
 * the page makes on the firm's behalf rather than a courtesy. It goes out inside
 * the request — there is no queue worker on this host — and the caller treats a
 * failure as non-fatal, because losing the enquiry would be far worse than
 * losing the receipt for it.
 */
class RfpAcknowledgement extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public RfpSubmission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Request received — '.$this->submission->reference,

            // Sent from the unattended address, but a reply has to reach a person.
            // A From that nobody reads and no Reply-To is how enquiries get lost.
            replyTo: [
                new Address(
                    config('company.email.rfp') ?: config('company.email.enquiries'),
                    config('company.legal_name'),
                ),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.rfp.acknowledgement',
            text: 'mail.rfp.acknowledgement-text',
            with: ['summary' => $this->summary()],
        );
    }

    /**
     * What the sender told us, read back so an error is obvious immediately
     * rather than at the scoping call.
     *
     * @return array<string, string>
     */
    private function summary(): array
    {
        $s = $this->submission;

        return array_filter([
            'Organisation' => $s->organisation,
            'Named contact' => trim($s->contact_name.($s->role ? ', '.$s->role : '')),
            'Engagement track' => $s->track?->label(),
            'Indicative budget' => $s->budget_band,
            'Timeline' => $s->timeline,
            'Country' => $s->country,
            'NDA requested' => $s->nda_required ? 'Yes — before any detail is exchanged' : null,
            'Submitted' => $s->created_at?->timezone('Africa/Nairobi')->format('j F Y, H:i').' EAT',
        ], fn ($v) => filled($v));
    }
}
