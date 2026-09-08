<?php

namespace Tests\Feature;

use App\Erp\Enums\ErpRole;
use App\Erp\Enums\ProjectRole;
use App\Erp\Enums\TaskStatus;
use App\Erp\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Who can reach what over HTTP.
 *
 * The workflow rules are tested separately; this is about the boundary — that
 * the delivery system is not reachable by a site visitor, that a site
 * administrator is not automatically an engineer, and that an engineer cannot
 * read an engagement they are not on.
 */
class DeliveryAccessTest extends TestCase
{
    use RefreshDatabase;

    private function engineer(array $attributes = []): User
    {
        return User::factory()->create([
            'erp_role' => ErpRole::Engineer,
            'is_active' => true,
            ...$attributes,
        ]);
    }

    private function project(?User $lead = null): Project
    {
        $project = Project::create([
            'code' => 'JTS-P-901', 'name' => 'Boundary test', 'slug' => 'boundary-test',
            'status' => 'active', 'default_branch' => 'main',
        ]);

        if ($lead) {
            $project->members()->attach($lead->id, ['role' => ProjectRole::Lead->value]);
        }

        return $project;
    }

    public function test_the_delivery_system_is_closed_to_visitors(): void
    {
        foreach (['/erp', '/erp/projects', '/erp/reviews'] as $path) {
            $this->get($path)->assertRedirect(route('erp.login'));
        }
    }

    public function test_the_delivery_system_is_never_indexable(): void
    {
        $this->get('/erp/login')->assertOk()->assertSee('noindex, nofollow', escape: false);
    }

    public function test_a_site_administrator_is_not_automatically_an_engineer(): void
    {
        // Console access and delivery access are separate grants on purpose:
        // closing someone's ERP account should not mean editing website
        // permissions, and vice versa.
        $admin = User::factory()->create(['is_admin' => true, 'erp_role' => null]);

        $this->actingAs($admin)->get('/erp')->assertForbidden();
    }

    public function test_a_suspended_account_cannot_reach_the_system(): void
    {
        $this->actingAs($this->engineer(['is_active' => false]))->get('/erp')->assertForbidden();
    }

    public function test_signing_in_with_no_delivery_role_is_refused_and_the_session_dropped(): void
    {
        $user = User::factory()->create(['erp_role' => null, 'password' => bcrypt('Str0ng!Passphrase#2026')]);

        $this->post(route('erp.login.store'), [
            'email' => $user->email,
            'password' => 'Str0ng!Passphrase#2026',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_an_engineer_signs_in_and_reaches_their_board(): void
    {
        $user = $this->engineer(['password' => bcrypt('Str0ng!Passphrase#2026')]);

        $this->post(route('erp.login.store'), [
            'email' => $user->email,
            'password' => 'Str0ng!Passphrase#2026',
        ])->assertRedirect(route('erp.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get('/erp')->assertOk()->assertSee('here is your board');
    }

    public function test_an_engineer_cannot_open_a_project_they_are_not_on(): void
    {
        $project = $this->project();

        $this->actingAs($this->engineer())
            ->get(route('erp.projects.show', $project))
            ->assertForbidden();
    }

    public function test_only_the_director_can_open_projects_or_enrol_people(): void
    {
        foreach ([route('erp.projects.create'), route('erp.people.index')] as $url) {
            $this->actingAs($this->engineer())->get($url)->assertForbidden();
        }

        $director = User::factory()->create(['erp_role' => ErpRole::DirectorOfDelivery, 'is_active' => true]);

        $this->actingAs($director)->get(route('erp.projects.create'))->assertOk();
        $this->actingAs($director)->get(route('erp.people.index'))->assertOk();
    }

    public function test_only_the_project_lead_can_add_work(): void
    {
        $lead = User::factory()->create(['erp_role' => ErpRole::PracticeLead, 'is_active' => true]);
        $project = $this->project($lead);
        $engineer = $this->engineer();
        $project->members()->attach($engineer->id, ['role' => ProjectRole::Engineer->value]);

        $payload = ['title' => 'New work', 'weight' => 2];

        $this->actingAs($engineer)
            ->post(route('erp.tasks.store', $project), $payload)
            ->assertForbidden();

        $this->actingAs($lead)
            ->post(route('erp.tasks.store', $project), $payload)
            ->assertRedirect();

        $this->assertSame(1, $project->tasks()->count());
    }

    public function test_a_rejection_without_a_note_is_refused(): void
    {
        $lead = User::factory()->create(['erp_role' => ErpRole::PracticeLead, 'is_active' => true]);
        $project = $this->project($lead);
        $engineer = $this->engineer();
        $project->members()->attach($engineer->id, ['role' => ProjectRole::Engineer->value]);

        $task = $project->tasks()->create([
            'reference' => 'JTS-P-901-001', 'title' => 'Work', 'assignee_id' => $engineer->id,
            'status' => TaskStatus::InReview, 'weight' => 1,
            'documentation' => 'Done.', 'branch' => 'feature/x',
        ]);

        $this->actingAs($lead)
            ->post(route('erp.tasks.transition', $task), ['to' => TaskStatus::ChangesRequested->value])
            ->assertSessionHasErrors('note');

        $this->assertSame(TaskStatus::InReview, $task->fresh()->status);
    }

    public function test_a_refused_transition_reports_the_reason_rather_than_failing_silently(): void
    {
        $lead = User::factory()->create(['erp_role' => ErpRole::PracticeLead, 'is_active' => true]);
        $project = $this->project($lead);
        $engineer = $this->engineer();
        $project->members()->attach($engineer->id, ['role' => ProjectRole::Engineer->value]);

        $task = $project->tasks()->create([
            'reference' => 'JTS-P-901-002', 'title' => 'Undocumented', 'assignee_id' => $engineer->id,
            'status' => TaskStatus::InProgress, 'weight' => 1,
        ]);

        $this->actingAs($engineer)
            ->post(route('erp.tasks.transition', $task), ['to' => TaskStatus::InReview->value])
            ->assertSessionHasErrors('transition');
    }

    public function test_the_public_site_carries_no_delivery_routes(): void
    {
        // The ERP lives on its own hostname in production. Locally it is under
        // /erp, so what matters is that no public page links into it.
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('/erp', $content);
    }
}
