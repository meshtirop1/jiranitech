<?php

namespace Tests\Feature;

use App\Mail\RfpAcknowledgement;
use App\Mail\RfpReceived;
use App\Models\RfpSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * The site tells every enquirer, in its own copy, that a qualified engineering
 * lead replies within two business days. That is a commitment the page makes on
 * the firm's behalf, so the receipt confirming it has to actually leave the
 * building — and the desk has to hear about the enquiry without anyone
 * remembering to open the console.
 */
class RfpAcknowledgementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'organisation' => 'Rift Valley Commercial Bank',
            'contact_name' => 'A. Kiprotich',
            'role' => 'Head of Technology',
            'email' => 'procurement@example.com',
            'telephone' => '+254 700 000000',
            'country' => 'Kenya',
            'track' => 'enterprise-modernisation',
            'budget_band' => 'USD 150,000 – 500,000',
            'timeline' => 'This quarter',
            'service_interests' => ['cloud-devops'],
            'scope_summary' => str_repeat('We need a full modernisation of the core ledger. ', 5),
            ...$overrides,
        ];
    }

    public function test_the_sender_is_acknowledged_and_the_desk_is_told(): void
    {
        $this->seed();
        Mail::fake();

        $this->post(route('rfp.store'), $this->payload())->assertRedirect();

        $submission = RfpSubmission::query()->sole();

        Mail::assertSent(RfpAcknowledgement::class, fn ($mail) => $mail->hasTo('procurement@example.com')
            && $mail->submission->is($submission));

        Mail::assertSent(RfpReceived::class, fn ($mail) => $mail->hasTo(config('company.email.rfp')));
    }

    public function test_the_acknowledgement_comes_from_no_reply_but_replies_reach_a_person(): void
    {
        $this->seed();

        $submission = RfpSubmission::factory()->create(['email' => 'procurement@example.com']);
        $envelope = (new RfpAcknowledgement($submission))->envelope();

        // A From nobody reads, with no Reply-To, is how an enquiry gets lost.
        $this->assertSame(
            config('company.email.rfp'),
            $envelope->replyTo[0]->address,
            'A reply to the acknowledgement must reach the proposals desk.',
        );

        $this->assertStringContainsString($submission->reference, $envelope->subject);
    }

    public function test_replying_to_the_desk_notification_reaches_the_enquirer(): void
    {
        $this->seed();

        $submission = RfpSubmission::factory()->create([
            'email' => 'procurement@example.com',
            'contact_name' => 'A. Kiprotich',
        ]);

        $this->assertSame(
            'procurement@example.com',
            (new RfpReceived($submission))->envelope()->replyTo[0]->address,
        );
    }

    public function test_the_acknowledgement_reads_back_what_was_submitted(): void
    {
        $this->seed();
        $this->post(route('rfp.store'), $this->payload());

        $submission = RfpSubmission::query()->sole();
        $body = (new RfpAcknowledgement($submission))->render();

        foreach ([
            $submission->reference,
            'Rift Valley Commercial Bank',
            'A. Kiprotich, Head of Technology',
            'Enterprise modernisation',
            'USD 150,000 – 500,000',
            config('company.response.substantive'),
        ] as $expected) {
            $this->assertStringContainsString($expected, $body, "The acknowledgement omits: {$expected}");
        }
    }

    public function test_the_acknowledgement_is_branded_and_carries_the_registered_particulars(): void
    {
        $this->seed();

        $submission = RfpSubmission::factory()->create();
        $body = (new RfpAcknowledgement($submission))->render();

        // The masthead, in the same cyan the site header uses.
        $this->assertStringContainsString('#0c6e92', $body);
        $this->assertStringContainsString('Jiranisoko', $body);

        // Someone checking a supplier finds the registry details in the footer.
        $this->assertStringContainsString(config('company.parent.registration_number'), $body);
        $this->assertStringContainsString(config('company.registered_address'), $body);

        // Mail clients ignore @font-face and strip <style>, so the brand face has
        // to be asked for inline, with a fallback that actually exists.
        $this->assertStringContainsString("font-family:'PT Sans',Arial,Helvetica,sans-serif", $body);
        $this->assertStringNotContainsString('@font-face', $body);
    }

    public function test_a_plain_text_alternative_ships_alongside_the_html(): void
    {
        $this->seed();

        $submission = RfpSubmission::factory()->create();
        $content = (new RfpAcknowledgement($submission))->content();

        $this->assertSame('mail.rfp.acknowledgement', $content->view);
        $this->assertSame('mail.rfp.acknowledgement-text', $content->text);
    }

    /**
     * The enquiry is the thing of value. A mail outage must not take it down
     * with it — the record is already saved and the confirmation page already
     * carries the reference.
     */
    public function test_a_mail_failure_does_not_lose_the_submission(): void
    {
        $this->seed();

        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP is down'));

        $this->post(route('rfp.store'), $this->payload())->assertRedirect();

        $this->assertSame(1, RfpSubmission::query()->count());
        $this->assertNotEmpty(RfpSubmission::query()->sole()->reference);
    }

    public function test_the_from_address_is_the_unattended_mailbox(): void
    {
        $this->assertSame('no-reply@jiranisokotech.co.ke', config('mail.from.address'));
        $this->assertSame('Jiranisoko Tech Solutions', config('mail.from.name'));
    }
}
