<?php

namespace Tests\Feature;

use App\Erp\Enums\ErpRole;
use App\Erp\Enums\ProjectRole;
use App\Erp\Enums\TaskStatus;
use App\Erp\Models\Project;
use App\Erp\Support\TaskWorkflow;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The delivery roles are the posts published at /company/leadership.
 *
 * That is the point of these assertions: what the company states in public about
 * who is accountable for what is the same thing the system enforces. If the two
 * drift apart, one of them is a lie, and it is cheaper to fail a test than to
 * discover which during an audit.
 */
class LeadershipPostsTest extends TestCase
{
    use RefreshDatabase;

    private function person(ErpRole $role): User
    {
        return User::factory()->create(['erp_role' => $role, 'is_active' => true]);
    }

    private function project(): Project
    {
        return Project::create([
            'code' => 'JTS-P-902', 'name' => 'Posts test', 'slug' => 'posts-test',
            'status' => 'active', 'default_branch' => 'main',
        ]);
    }

    public function test_every_published_leadership_post_exists_as_a_role(): void
    {
        $this->seed();

        $published = TeamMember::query()->pluck('role_title');
        $roles = collect(ErpRole::cases())->map->label();

        foreach ($published as $post) {
            // The three practice leads share one role and carry their discipline
            // in job_title, so they match on the prefix.
            $matched = $roles->contains(fn (string $label) => str_starts_with($post, $label));

            $this->assertTrue($matched, "The published post \"{$post}\" has no delivery role.");
        }
    }

    // --- who may create accounts --------------------------------------------

    public function test_leadership_enrols_people_not_only_the_directors(): void
    {
        foreach ([
            ErpRole::ManagingDirector,
            ErpRole::ChiefTechnologyOfficer,
            ErpRole::DirectorOfDelivery,
            ErpRole::PracticeLead,
        ] as $role) {
            $this->actingAs($this->person($role))
                ->get(route('erp.people.index'))
                ->assertOk("{$role->label()} should be able to enrol.");
        }
    }

    /**
     * The navigation has to ask the same question the route asks. A practice
     * lead allowed through the middleware but never shown the link is allowed
     * in theory only.
     */
    public function test_the_navigation_offers_people_to_everyone_who_may_enrol(): void
    {
        $link = route('erp.people.index');

        $this->actingAs($this->person(ErpRole::PracticeLead))
            ->get(route('erp.dashboard'))
            ->assertOk()
            ->assertSee($link);

        $this->actingAs($this->person(ErpRole::Engineer))
            ->get(route('erp.dashboard'))
            ->assertOk()
            ->assertDontSee($link);
    }

    public function test_an_engineer_cannot_reach_the_people_page(): void
    {
        $this->actingAs($this->person(ErpRole::Engineer))
            ->get(route('erp.people.index'))
            ->assertForbidden();
    }

    public function test_a_practice_lead_may_enrol_an_engineer(): void
    {
        $this->actingAs($this->person(ErpRole::PracticeLead))
            ->post(route('erp.people.store'), [
                'name' => 'New Engineer',
                'email' => 'new.engineer@jiranisokotech.co.ke',
                'erp_role' => ErpRole::Engineer->value,
                'password' => 'Str0ng!Passphrase#2026',
                'password_confirmation' => 'Str0ng!Passphrase#2026',
            ])
            ->assertSessionHasNoErrors();

        $created = User::query()->where('email', 'new.engineer@jiranisokotech.co.ke')->sole();
        $this->assertSame(ErpRole::Engineer, $created->erpRole());
    }

    /**
     * The containment that makes delegated enrolment safe: being able to create
     * an account must not be a way to hand out authority nobody appointed.
     */
    public function test_a_practice_lead_cannot_appoint_a_director(): void
    {
        $this->actingAs($this->person(ErpRole::PracticeLead))
            ->post(route('erp.people.store'), [
                'name' => 'Self Promotion',
                'email' => 'nope@jiranisokotech.co.ke',
                'erp_role' => ErpRole::ManagingDirector->value,
                'password' => 'Str0ng!Passphrase#2026',
                'password_confirmation' => 'Str0ng!Passphrase#2026',
            ])
            ->assertSessionHasErrors('erp_role');

        $this->assertDatabaseMissing('users', ['email' => 'nope@jiranisokotech.co.ke']);
    }

    public function test_only_a_director_may_change_somebody_elses_post(): void
    {
        $engineer = $this->person(ErpRole::Engineer);

        $this->actingAs($this->person(ErpRole::PracticeLead))
            ->put(route('erp.people.update', $engineer), [
                'erp_role' => ErpRole::PracticeLead->value,
                'is_active' => 1,
            ])
            ->assertForbidden();

        $this->actingAs($this->person(ErpRole::DirectorOfDelivery))
            ->put(route('erp.people.update', $engineer), [
                'erp_role' => ErpRole::PracticeLead->value,
                'is_active' => 1,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(ErpRole::PracticeLead, $engineer->fresh()->erpRole());
    }

    public function test_the_last_person_able_to_open_a_project_cannot_stand_themselves_down(): void
    {
        $director = $this->person(ErpRole::DirectorOfDelivery);

        $this->actingAs($director)
            ->put(route('erp.people.update', $director), [
                'erp_role' => ErpRole::Engineer->value,
                'is_active' => 1,
            ])
            ->assertSessionHasErrors('erp_role');

        $this->assertSame(ErpRole::DirectorOfDelivery, $director->fresh()->erpRole());
    }

    // --- who may open engagements -------------------------------------------

    public function test_opening_an_engagement_is_reserved_to_the_three_directing_posts(): void
    {
        $allowed = [ErpRole::ManagingDirector, ErpRole::ChiefTechnologyOfficer, ErpRole::DirectorOfDelivery];

        foreach (ErpRole::cases() as $role) {
            $response = $this->actingAs($this->person($role))->get(route('erp.projects.create'));

            in_array($role, $allowed, true)
                ? $response->assertOk("{$role->label()} should be able to open a project.")
                : $response->assertForbidden("{$role->label()} should not be able to open a project.");
        }
    }

    // --- the Data Protection Officer's statutory independence ---------------

    public function test_the_data_protection_officer_reads_every_engagement(): void
    {
        $dpo = $this->person(ErpRole::DataProtectionOfficer);

        $this->assertTrue($this->project()->mayBeSeenBy($dpo));
    }

    public function test_the_data_protection_officer_casts_no_review(): void
    {
        // The leadership page states the post is independent of delivery, which
        // is a requirement of the Data Protection Act 2019 rather than a
        // preference. Independence the system quietly allows to be broken is not
        // independence.
        $dpo = $this->person(ErpRole::DataProtectionOfficer);
        $project = $this->project();
        $engineer = $this->person(ErpRole::Engineer);
        $project->members()->attach($engineer->id, ['role' => ProjectRole::Engineer->value]);

        $this->assertFalse($project->mayBeReviewedBy($dpo));

        $task = $project->tasks()->create([
            'reference' => 'JTS-P-902-001', 'title' => 'Work', 'assignee_id' => $engineer->id,
            'status' => TaskStatus::InReview, 'weight' => 1,
            'documentation' => 'Done.', 'branch' => 'feature/x',
        ]);

        $this->assertStringContainsString(
            'reviewer',
            strtolower((string) TaskWorkflow::refusal($task, TaskStatus::Approved, $dpo)),
        );
    }

    public function test_the_data_protection_officer_is_never_offered_as_an_assignee(): void
    {
        $dpo = $this->person(ErpRole::DataProtectionOfficer);
        $lead = $this->person(ErpRole::DirectorOfDelivery);
        $project = $this->project();

        $project->members()->attach($lead->id, ['role' => ProjectRole::Lead->value]);
        $project->members()->attach($dpo->id, ['role' => ProjectRole::Engineer->value]);

        $names = $this->actingAs($lead)
            ->get(route('erp.projects.show', $project))
            ->assertOk()
            ->viewData('assignable')
            ->pluck('name');

        $this->assertFalse($names->contains($dpo->name), 'The DPO was offered as an assignee.');
        $this->assertFalse($dpo->erpRole()->mayHoldWork());
    }

    // --- who may review ------------------------------------------------------

    public function test_the_head_of_information_security_may_halt_any_release(): void
    {
        // The leadership page claims authority to halt a production release.
        // Here that is the right to review — and therefore to refuse — anywhere.
        $hois = $this->person(ErpRole::HeadOfInformationSecurity);

        $this->assertTrue($this->project()->mayBeReviewedBy($hois));
        $this->assertTrue($hois->erpRole()->reviewsAnywhere());
    }

    public function test_a_practice_lead_reviews_only_on_their_own_projects(): void
    {
        $lead = $this->person(ErpRole::PracticeLead);
        $theirs = $this->project();
        $theirs->members()->attach($lead->id, ['role' => ProjectRole::Lead->value]);

        $other = Project::create([
            'code' => 'JTS-P-903', 'name' => 'Someone elses', 'slug' => 'someone-elses',
            'status' => 'active', 'default_branch' => 'main',
        ]);

        $this->assertTrue($theirs->mayBeReviewedBy($lead));
        $this->assertFalse($other->mayBeReviewedBy($lead));
        $this->assertFalse($other->mayBeSeenBy($lead));
    }
}
