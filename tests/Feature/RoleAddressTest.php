<?php

namespace Tests\Feature;

use App\Models\JobOpening;
use App\Support\PublicationGates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The addresses the site publishes.
 *
 * Every one is a role, not a person: a shared mailbox survives a departure, a
 * holiday and a reorganisation, which is exactly why procurement asks for role
 * addresses. They are also all on the corporate domain — an enterprise vendor
 * that answers from a free mail provider does not clear a bank's supplier check.
 */
class RoleAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function published(): array
    {
        return [
            'enquiries' => config('company.email.enquiries'),
            'rfp' => config('company.email.rfp'),
            'security' => config('company.email.security'),
            'careers' => config('company.email.careers'),
            'privacy' => config('company.email.privacy'),
        ];
    }

    public function test_every_published_address_shares_one_corporate_domain(): void
    {
        // Deliberately not compared against app.url: that is http://localhost in
        // the test environment, and the mail domain is its own fact anyway. What
        // matters is that the five agree with each other and none is a freebie.
        $domains = [];

        foreach ($this->published() as $role => $address) {
            $this->assertNotEmpty($address, "No address configured for {$role}.");
            $this->assertStringContainsString('@', $address, "The {$role} address is malformed.");
            $domains[$role] = substr($address, strpos($address, '@') + 1);
        }

        $this->assertCount(
            1,
            array_unique($domains),
            'The published addresses are spread across more than one domain: '
            .implode(', ', array_unique($domains)),
        );
    }

    public function test_no_address_is_a_free_provider_or_a_persons_name(): void
    {
        foreach ($this->published() as $role => $address) {
            [$local, $domain] = explode('@', $address);

            foreach (['gmail', 'yahoo', 'hotmail', 'outlook.com'] as $consumer) {
                $this->assertStringNotContainsString(
                    $consumer,
                    $domain,
                    "The {$role} address is on a consumer mail provider.",
                );
            }

            // A role, not an individual. Catches meshack@, tirop@, m.tirop@ and
            // anything else that walks out of the door with one person.
            $this->assertDoesNotMatchRegularExpression(
                '/^(meshack|tirop|meshacktirop|m\.?tirop)/i',
                $local,
                "The {$role} address is named after a person rather than a role.",
            );
        }
    }

    public function test_the_disclosure_policy_publishes_the_rfc_2142_security_address(): void
    {
        $this->seed();

        // security@ is the name other operators and researchers look for first.
        $this->assertStringStartsWith('security@', config('company.email.security'));

        $this->get('/legal/responsible-disclosure')
            ->assertOk()
            ->assertSee(config('company.email.security'));
    }

    public function test_applications_go_to_the_careers_mailbox_not_the_general_one(): void
    {
        $this->seed();

        $job = JobOpening::query()->first();
        $job->update(['is_published' => true, 'posted_at' => now()]);

        $content = $this->get('/company/careers')->assertOk()->getContent();

        $this->assertStringContainsString('mailto:'.config('company.email.careers'), $content);
        $this->assertStringNotContainsString('mailto:'.config('company.email.enquiries').'?subject=Application', $content);
    }

    public function test_the_privacy_notice_publishes_a_data_protection_route(): void
    {
        $this->seed();

        $this->get('/legal/privacy-notice')
            ->assertOk()
            ->assertSee(config('company.email.privacy'));
    }

    public function test_the_footer_and_engagement_desk_carry_the_enquiries_address(): void
    {
        $this->seed();

        foreach (['/', '/contact/engagement-desk'] as $path) {
            $this->get($path)->assertOk()->assertSee(config('company.email.enquiries'));
        }
    }

    /**
     * G-07 wanted a registered office, a company number and a contact route. The
     * CR12 supplied the first two; this supplies the third.
     */
    public function test_gate_g07_is_now_closed(): void
    {
        $this->seed();

        $gate = collect(PublicationGates::all())->firstWhere('id', 'G-07');

        $this->assertTrue($gate['closed'], 'G-07 is still open: '.$gate['detail']);

        $this->get('/')->assertOk()->assertDontSee('Gate G-07');
    }
}
