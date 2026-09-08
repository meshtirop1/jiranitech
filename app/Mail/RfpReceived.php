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
 * Sent to the proposals mailbox when a request arrives.
 *
 * Without it the two-business-day promise on the site depends on somebody
 * remembering to open the console. Reply-To is the enquirer, so answering the
 * notification answers them directly.
 */
class RfpReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public RfpSubmission $submission) {}

    public function envelope(): Envelope
    {
        $s = $this->submission;

        return new Envelope(
            subject: '['.$s->reference.'] '.$s->organisation.' — '.($s->track?->label() ?? 'RFP'),
            replyTo: [new Address($s->email, $s->contact_name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.rfp.received',
            text: 'mail.rfp.received-text',
        );
    }
}
