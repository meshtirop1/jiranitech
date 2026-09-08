<?php

namespace Tests\Feature;

use App\Erp\Enums\ErpRole;
use App\Erp\Enums\TaskStatus;
use App\Erp\Models\ActivityEntry;
use App\Erp\Models\Project;
use App\Erp\Models\Task;
use App\Erp\Support\DemoData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The worked example, and the fact that it can only ever be loaded into an
 * empty division and can always be taken back out again.
 *
 * A seeding button on a production system is only safe because of when it is
 * reachable, so that is what most of these assert.
 */
class DemoDataTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        return User::factory()->create(['is_admin' => true, 'erp_role' => null, 'is_active' => true]);
    }

    public function test_the_standing_in_administrator_may_load_it(): void
    {
        $this->actingAs($this->administrator())
            ->post(route('erp.demo.store'))
            ->assertRedirect(route('erp.login'));

        $this->assertTrue(DemoData::exists());
    }

    public function test_it_plants_every_published_post_and_work_in_every_state(): void
    {
        DemoData::plant();

        $roles = User::query()->where('email', 'like', '%@'.DemoData::DOMAIN)
            ->get()->map->erpRole()->unique();

        foreach (ErpRole::cases() as $role) {
            $this->assertTrue($roles->contains($role), "No demonstration account holds {$role->label()}.");
        }

        $states = Task::query()->get()->map->status->unique();

        foreach (TaskStatus::cases() as $state) {
            $this->assertTrue($states->contains($state), "No demonstration task is {$state->label()}.");
        }
    }

    public function test_every_account_shares_the_published_password(): void
    {
        DemoData::plant();

        foreach (User::query()->where('email', 'like', '%@'.DemoData::DOMAIN)->get() as $user) {
            $this->assertTrue(
                Hash::check(DemoData::PASSWORD, $user->password),
                "{$user->email} does not take the published demonstration password.",
            );
        }
    }

    public function test_a_demonstration_account_can_actually_sign_in(): void
    {
        DemoData::plant();

        $this->post(route('erp.login.store'), [
            'email' => 'md@'.DemoData::DOMAIN,
            'password' => DemoData::PASSWORD,
        ])->assertRedirect(route('erp.dashboard'));

        $this->assertAuthenticated();
    }

    // --- when it is reachable ------------------------------------------------

    public function test_it_cannot_be_loaded_twice(): void
    {
        $admin = $this->administrator();
        DemoData::plant();

        // Planting appointed a Managing Director, so the founding post has
        // lapsed and the door this came through is shut.
        $this->actingAs($admin->fresh())
            ->post(route('erp.demo.store'))
            ->assertForbidden();

        $this->assertSame(13, User::query()->where('email', 'like', '%@'.DemoData::DOMAIN)->count());
    }

    public function test_it_cannot_be_loaded_into_a_division_doing_real_work(): void
    {
        $admin = $this->administrator();
        User::factory()->create(['erp_role' => ErpRole::DirectorOfDelivery, 'is_active' => true]);

        $this->actingAs($admin->fresh())
            ->post(route('erp.demo.store'))
            ->assertForbidden();

        $this->assertFalse(DemoData::exists());
    }

    public function test_a_director_who_is_not_standing_in_may_also_load_it(): void
    {
        // The account that hit this in production held a real directing post,
        // not the founding loan, and had no way to reach the button.
        $director = User::factory()->create(['erp_role' => ErpRole::DirectorOfDelivery, 'is_active' => true]);

        $this->actingAs($director)
            ->post(route('erp.demo.store'))
            ->assertRedirect(route('erp.people.index'));

        $this->assertTrue(DemoData::exists());
    }

    public function test_it_refuses_once_any_engagement_exists(): void
    {
        $director = User::factory()->create(['erp_role' => ErpRole::DirectorOfDelivery, 'is_active' => true]);

        Project::create([
            'code' => 'JTS-P-001', 'name' => 'Real work', 'slug' => 'real-work',
            'status' => 'active', 'default_branch' => 'main',
        ]);

        $this->actingAs($director)
            ->post(route('erp.demo.store'))
            ->assertStatus(409);

        $this->assertFalse(DemoData::exists());
    }

    public function test_an_engineer_cannot_load_it(): void
    {
        $this->actingAs(User::factory()->create(['erp_role' => ErpRole::Engineer, 'is_active' => true]))
            ->post(route('erp.demo.store'))
            ->assertForbidden();
    }

    // --- clearing ------------------------------------------------------------

    public function test_a_director_may_clear_it_and_nothing_is_left_behind(): void
    {
        DemoData::plant();
        $md = User::query()->where('email', 'md@'.DemoData::DOMAIN)->sole();

        $this->actingAs($md)->delete(route('erp.demo.destroy'))->assertRedirect();

        $this->assertFalse(DemoData::exists());
        $this->assertSame(0, Project::query()->count());
        $this->assertSame(0, Task::query()->count());
        $this->assertSame(0, ActivityEntry::query()->count());
        $this->assertSame(0, User::query()->where('email', 'like', '%@'.DemoData::DOMAIN)->count());
    }

    public function test_clearing_leaves_real_work_untouched(): void
    {
        // The guard that matters: remove() is addressed at the marked set, not
        // at "everything in the delivery system".
        DemoData::plant();

        $real = Project::create([
            'code' => 'JTS-P-001', 'name' => 'A real engagement', 'slug' => 'real-one',
            'status' => 'active', 'default_branch' => 'main',
        ]);
        $engineer = User::factory()->create(['erp_role' => ErpRole::Engineer, 'is_active' => true]);
        $task = $real->tasks()->create([
            'reference' => 'JTS-P-001-001', 'title' => 'Real work',
            'assignee_id' => $engineer->id, 'status' => TaskStatus::InProgress, 'weight' => 3,
        ]);
        ActivityEntry::record($task, 'created', $engineer);

        DemoData::remove();

        $this->assertDatabaseHas('projects', ['code' => 'JTS-P-001']);
        $this->assertDatabaseHas('tasks', ['reference' => 'JTS-P-001-001']);
        $this->assertDatabaseHas('users', ['id' => $engineer->id]);
        $this->assertSame(1, ActivityEntry::query()->count());
    }

    public function test_the_administrator_gets_the_division_back_after_clearing(): void
    {
        $admin = $this->administrator();
        DemoData::plant();

        $this->assertNull($admin->fresh()->erpRole());

        DemoData::remove();

        $this->assertSame(ErpRole::ManagingDirector, $admin->fresh()->erpRole());
    }

    public function test_an_engineer_cannot_clear_it(): void
    {
        DemoData::plant();

        $this->actingAs(User::factory()->create(['erp_role' => ErpRole::Engineer, 'is_active' => true]))
            ->delete(route('erp.demo.destroy'))
            ->assertForbidden();

        $this->assertTrue(DemoData::exists());
    }

    // --- what it looks like once loaded -------------------------------------

    public function test_the_lead_opens_on_a_review_queue_and_the_engineer_on_work(): void
    {
        DemoData::plant();

        $lead = User::query()->where('email', 'lead.payments@'.DemoData::DOMAIN)->sole();
        $engineer = User::query()->where('email', 'eng.mutai@'.DemoData::DOMAIN)->sole();

        $this->assertGreaterThan(0, $lead->reviewQueue()->count(),
            'The payments lead should have something waiting to be reviewed.');
        $this->assertGreaterThan(0, $engineer->openWork()->count(),
            'The engineer should open on assigned work.');
    }

    public function test_progress_is_computed_on_the_planted_engagement(): void
    {
        DemoData::plant();

        $progress = Project::query()->where('code', DemoData::PREFIX.'-001')->sole()->progress();

        $this->assertGreaterThan(0, $progress['percent']);
        $this->assertLessThan(100, $progress['percent']);
    }
}
