<?php

namespace Tests\Feature;

use App\Enums\SlaTier;
use App\Models\Setting;
use App\Models\User;
use App\Support\PublicationGates;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Commercial commitments that used to be constants.
 *
 * Every figure in the SLA table is something a client can hold the firm to, and
 * the only way to correct one was a code change and a deploy — on a host with no
 * shell. These assert that the console reaches them, that editing one moves what
 * the public page says, and that changing a figure withdraws any ratification
 * that was given for the figures it replaced.
 */
class ServiceLevelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true, 'is_active' => true]);
    }

    /** @return array<string, string> */
    private function payload(array $overrides = []): array
    {
        $data = [];

        foreach (SlaTier::cases() as $tier) {
            foreach (array_keys(SlaTier::fields()) as $field) {
                $data['sla_'.$tier->value.'_'.$field] = (string) config('sla.tiers.'.$tier->value.'.'.$field);
            }

            if ($tier->hasServiceCredits()) {
                $data['sla_'.$tier->value.'_service_credits'] = '1';
            }
        }

        return [...$data, ...$overrides];
    }

    public function test_the_screen_opens_and_is_closed_to_everyone_else(): void
    {
        // Guest first: actingAs persists for the rest of the test.
        $this->get(route('admin.sla.edit'))->assertRedirect();

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.sla.edit'))
            ->assertForbidden();

        $this->actingAs($this->admin())
            ->get(route('admin.sla.edit'))
            ->assertOk()
            ->assertSee('Availability target');
    }

    public function test_the_defaults_still_read_from_config(): void
    {
        $this->assertSame('99.95%', SlaTier::Platinum->availabilityTarget());
        $this->assertSame('15 minutes', SlaTier::Platinum->priorityOneResponse());
        $this->assertTrue(SlaTier::Gold->hasServiceCredits());
        $this->assertFalse(SlaTier::Silver->hasServiceCredits());
    }

    public function test_editing_a_target_changes_what_the_public_table_says(): void
    {
        $this->seed();

        $this->actingAs($this->admin())
            ->put(route('admin.sla.update'), $this->payload([
                'sla_platinum_availability_target' => '99.9%',
                'sla_platinum_p1_response' => '30 minutes',
            ]))
            ->assertSessionHasNoErrors();

        SiteSettings::applyToConfig();

        $this->assertSame('99.9%', SlaTier::Platinum->availabilityTarget());

        $this->get(route('home'))->assertOk()->assertSee('30 minutes');
    }

    public function test_service_credits_can_be_turned_off(): void
    {
        // A boolean needs its own path: "0" is a real answer, not a missing one,
        // so it cannot be filtered out the way an empty string is.
        $this->seed();

        $payload = $this->payload();
        unset($payload['sla_gold_service_credits']);

        $this->actingAs($this->admin())
            ->put(route('admin.sla.update'), $payload)
            ->assertSessionHasNoErrors();

        SiteSettings::applyToConfig();

        $this->assertFalse(SlaTier::Gold->hasServiceCredits());
    }

    public function test_a_target_cannot_be_emptied(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.sla.update'), $this->payload([
                'sla_gold_availability_target' => '',
            ]))
            ->assertSessionHasErrors('sla_gold_availability_target');
    }

    // --- ratification ---------------------------------------------------------

    public function test_ratifying_closes_gate_g05(): void
    {
        $this->seed();

        $gate = collect(PublicationGates::all())->firstWhere('id', 'G-05');
        $this->assertFalse($gate['closed']);

        $this->actingAs($this->admin())
            ->put(route('admin.sla.update'), $this->payload(['ratified' => '1']))
            ->assertSessionHasNoErrors();

        $gate = collect(PublicationGates::all())->firstWhere('id', 'G-05');
        $this->assertTrue($gate['closed']);
    }

    public function test_changing_a_figure_withdraws_the_ratification_given_for_the_old_one(): void
    {
        // Otherwise a target could be raised after sign-off and keep the sign-off
        // that was given for a different, lower number.
        $this->seed();
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('admin.sla.update'), $this->payload(['ratified' => '1']));
        $this->assertTrue(SlaTier::ratified());

        $this->actingAs($admin)->put(route('admin.sla.update'), $this->payload([
            'sla_platinum_availability_target' => '99.99%',
        ]));

        Setting::flush();

        $this->assertFalse(SlaTier::ratified(), 'A new figure kept an old ratification.');
    }

    public function test_the_gate_points_at_the_screen_that_decides_it(): void
    {
        $gate = collect(PublicationGates::all())->firstWhere('id', 'G-05');

        $this->assertSame('admin.sla.edit', $gate['route']);
    }

    public function test_every_editable_field_is_carried_into_config(): void
    {
        // The form, its validation and the settings map are all built from
        // SlaTier::fields(). This asserts nothing can be added to the form and
        // then silently fail to persist.
        $map = SiteSettings::slaMap();

        foreach (SlaTier::cases() as $tier) {
            foreach (array_keys(SlaTier::fields()) as $field) {
                $this->assertArrayHasKey('sla_'.$tier->value.'_'.$field, $map);
            }

            $this->assertArrayHasKey('sla_'.$tier->value.'_service_credits', $map);
        }
    }
}
