<?php

namespace Tests\Feature;

use App\Erp\Enums\ErpRole;
use App\Erp\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The loan that lets the first leader in, and gives itself back.
 *
 * Every account here is created by somebody who already holds a post, so an
 * empty division could otherwise never be started. The site administrator
 * stands in as Managing Director while nobody is in charge — and stops the
 * moment somebody is. These assertions are the reason that is a bootstrap and
 * not a back door.
 */
class FoundingPostTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'erp_role' => null,
            'is_active' => true,
        ]);
    }

    public function test_the_administrator_stands_in_while_nobody_is_in_charge(): void
    {
        $admin = $this->administrator();

        $this->assertSame(ErpRole::ManagingDirector, $admin->erpRole());
        $this->assertTrue($admin->worksInDelivery());
    }

    public function test_the_loan_lapses_once_somebody_holds_a_directing_post(): void
    {
        $admin = $this->administrator();
        User::factory()->create(['erp_role' => ErpRole::DirectorOfDelivery, 'is_active' => true]);

        $this->assertNull($admin->fresh()->erpRole());
        $this->assertFalse($admin->fresh()->worksInDelivery());
    }

    public function test_a_director_who_is_stood_down_hands_the_post_back(): void
    {
        // Break-glass, not a one-time grant: if the last director is deactivated
        // the division would otherwise be locked out for good.
        $admin = $this->administrator();
        $director = User::factory()->create(['erp_role' => ErpRole::DirectorOfDelivery, 'is_active' => true]);

        $this->assertNull($admin->fresh()->erpRole());

        $director->update(['is_active' => false]);

        $this->assertSame(ErpRole::ManagingDirector, $admin->fresh()->erpRole());
    }

    public function test_a_practice_lead_alone_does_not_end_the_vacancy(): void
    {
        // A practice lead may enrol engineers but cannot open an engagement, so
        // the division still has nobody in charge of it.
        $admin = $this->administrator();
        User::factory()->create(['erp_role' => ErpRole::PracticeLead, 'is_active' => true]);

        $this->assertSame(ErpRole::ManagingDirector, $admin->fresh()->erpRole());
    }

    public function test_an_ordinary_account_never_holds_the_founding_post(): void
    {
        $nobody = User::factory()->create(['is_admin' => false, 'erp_role' => null, 'is_active' => true]);

        $this->assertNull($nobody->erpRole());
        $this->assertFalse($nobody->worksInDelivery());
    }

    public function test_a_deactivated_administrator_holds_nothing(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'erp_role' => null, 'is_active' => false]);

        $this->assertNull($admin->erpRole());
        $this->assertFalse($admin->worksInDelivery());
    }

    public function test_the_standing_in_administrator_may_enrol_the_first_leader(): void
    {
        $this->actingAs($this->administrator())
            ->post(route('erp.people.store'), [
                'name' => 'First Director',
                'email' => 'first.director@jiranisokotech.co.ke',
                'erp_role' => ErpRole::ManagingDirector->value,
                'password' => 'Str0ng!Passphrase#2026',
                'password_confirmation' => 'Str0ng!Passphrase#2026',
            ])
            ->assertSessionHasNoErrors();

        $appointed = User::query()->where('email', 'first.director@jiranisokotech.co.ke')->sole();
        $this->assertSame(ErpRole::ManagingDirector, $appointed->erpRole());
    }

    public function test_standing_in_is_not_the_same_as_being_available_for_work(): void
    {
        // The separation the delivery middleware keeps: administering the website
        // does not make somebody an engineer. They may direct; they are never
        // offered as an assignee.
        $admin = $this->administrator();

        $this->assertFalse(User::query()->inDelivery()->get()->contains('id', $admin->id));
    }

    public function test_the_project_form_says_so_rather_than_offering_an_empty_list(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('erp.projects.create'))
            ->assertOk()
            ->assertSee('Nobody is enrolled yet');
    }

    public function test_the_standing_in_administrator_sees_the_delivery_system(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)->get(route('erp.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('erp.people.index'))->assertOk();

        $project = Project::create([
            'code' => 'JTS-P-904', 'name' => 'Founding', 'slug' => 'founding',
            'status' => 'active', 'default_branch' => 'main',
        ]);

        $this->assertTrue($project->mayBeSeenBy($admin));
    }
}
