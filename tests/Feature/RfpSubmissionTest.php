<?php

namespace Tests\Feature;

use App\Enums\RfpTrack;
use App\Models\RfpSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RfpSubmissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'track' => RfpTrack::EnterpriseModernisation->value,
            'organisation' => 'Rift Valley Commercial Bank',
            'contact_name' => 'A. Kiprotich',
            'role' => 'Head of Technology',
            'email' => 'procurement@jiranisoko.com',
            'telephone' => '+254 700 000000',
            'country' => 'Kenya',
            'budget_band' => 'USD 150,000 – 500,000',
            'timeline' => 'This quarter',
            'scope_summary' => 'We run a core banking platform on end-of-life infrastructure and need it migrated without a service interruption longer than four hours.',
            'service_interests' => ['cloud-devops', 'fintech-payments'],
            'nda_required' => '1',
            ...$overrides,
        ];
    }

    public function test_the_intake_form_renders_every_track(): void
    {
        $this->seed();

        $response = $this->get(route('rfp.create'))->assertOk();

        foreach (RfpTrack::cases() as $track) {
            $response->assertSee($track->label());
        }
    }

    public function test_a_track_query_parameter_preselects_that_option(): void
    {
        $this->seed();

        $response = $this->get(route('rfp.create', ['track' => RfpTrack::TeamAugmentation->value]))->assertOk();

        // The input is rendered across several lines, so match the attribute pair
        // rather than an exact adjacent string.
        $this->assertMatchesRegularExpression(
            '/value="'.preg_quote(RfpTrack::TeamAugmentation->value, '/').'"\s+checked/',
            $response->getContent(),
        );
    }

    public function test_a_valid_submission_is_stored_and_given_a_reference(): void
    {
        $this->seed();

        $response = $this->post(route('rfp.store'), $this->validPayload());

        $submission = RfpSubmission::query()->sole();

        $this->assertSame('Rift Valley Commercial Bank', $submission->organisation);
        $this->assertSame(RfpTrack::EnterpriseModernisation, $submission->track);
        $this->assertTrue($submission->nda_required);
        $this->assertSame(['cloud-devops', 'fintech-payments'], $submission->service_interests);
        $this->assertMatchesRegularExpression('/^JTS-RFP-\d{8}-[A-Z0-9]{5}$/', $submission->reference);

        $response->assertRedirect();
    }

    public function test_the_confirmation_page_requires_a_signed_link(): void
    {
        $this->seed();

        $redirect = $this->post(route('rfp.store'), $this->validPayload());
        $signedUrl = $redirect->headers->get('Location');

        $this->get($signedUrl)
            ->assertOk()
            ->assertSee('Your reference is')
            ->assertSee('Enterprise modernisation');

        // The same page without a valid signature must not be reachable.
        $submission = RfpSubmission::query()->sole();
        $this->get(route('rfp.confirmation', $submission))->assertForbidden();
    }

    public function test_a_one_line_brief_is_rejected_with_a_useful_message(): void
    {
        $this->seed();

        $this->post(route('rfp.store'), $this->validPayload(['scope_summary' => 'Need a website.']))
            ->assertSessionHasErrors('scope_summary');

        $this->assertSame(0, RfpSubmission::query()->count());
    }

    public function test_track_and_contact_details_are_required(): void
    {
        $this->seed();

        $this->post(route('rfp.store'), [])
            ->assertSessionHasErrors(['track', 'organisation', 'contact_name', 'email', 'scope_summary']);
    }

    public function test_an_unknown_service_interest_is_rejected(): void
    {
        $this->seed();

        $this->post(route('rfp.store'), $this->validPayload(['service_interests' => ['not-a-pillar']]))
            ->assertSessionHasErrors('service_interests.0');
    }
}
