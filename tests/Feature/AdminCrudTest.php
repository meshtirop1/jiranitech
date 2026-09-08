<?php

namespace Tests\Feature;

use App\Enums\ComplianceStatus;
use App\Models\ComplianceClaim;
use App\Models\JobOpening;
use App\Models\Metric;
use App\Models\PlatformReference;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Adding and removing records, not only editing the ones that were seeded.
 *
 * The console could edit a seeded row and nothing else, so the site had exactly
 * the posts, roles, metrics, standards and platforms it shipped with. That reads
 * as a fixture rather than something the business owns.
 *
 * The gates survive creation, which is the part worth asserting: a new metric
 * arrives unpublished, a new vacancy is not advertised, and a new platform's
 * scale figures are not disclosed. None of those is a checkbox default.
 */
class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true, 'is_active' => true]);
    }

    public function test_a_leadership_post_can_be_added_and_removed(): void
    {
        $this->seed();
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.team.store'), [
            'role_title' => 'Head of Quality Engineering',
            'accountability' => 'Owns the test strategy across every engagement.',
            'is_published' => '1',
        ])->assertSessionHasNoErrors();

        $post = TeamMember::query()->where('role_title', 'Head of Quality Engineering')->sole();

        $this->get(route('company.leadership'))->assertOk()->assertSee('Head of Quality Engineering');

        $this->actingAs($admin)->delete(route('admin.team.destroy', $post))->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('team_members', ['id' => $post->id]);
    }

    public function test_a_new_vacancy_is_created_unadvertised(): void
    {
        $this->seed();

        $this->actingAs($this->admin())->post(route('admin.jobs.store'), [
            'title' => 'Staff Reliability Engineer',
            'slug' => 'staff-reliability-engineer',
            'level' => 'Staff',
            'location' => 'Eldoret, Kenya',
            'arrangement' => 'Hybrid',
            'summary' => 'Owns the reliability of what we operate.',
            'responsibilities' => "Run the on-call rota\nOwn the error budget",
        ])->assertSessionHasNoErrors();

        $job = JobOpening::query()->where('slug', 'staff-reliability-engineer')->sole();

        $this->assertFalse($job->is_published, 'A new role must not advertise itself.');
        $this->assertNull($job->posted_at);
        $this->assertSame(['Run the on-call rota', 'Own the error budget'], $job->responsibilities);

        $this->get(route('company.careers'))->assertOk()->assertDontSee('Staff Reliability Engineer');
    }

    public function test_a_new_metric_is_created_unpublished_and_needs_a_basis(): void
    {
        $this->seed();
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.metrics.store'), [
            'key' => 'engineers_on_staff',
            'label' => 'Engineers on staff',
            'value' => '22',
            'basis' => 'Headcount at 1 September 2026, permanent and contract, from the payroll register.',
            'effective_on' => '2026-09-01',
        ])->assertSessionHasNoErrors();

        $metric = Metric::query()->where('key', 'engineers_on_staff')->sole();
        $this->assertFalse($metric->is_published, 'A figure must not publish itself.');

        // The rule that gate G-01 exists for, applied at creation as well as edit.
        $this->actingAs($admin)->post(route('admin.metrics.store'), [
            'key' => 'unsupported_claim',
            'label' => 'Something impressive',
            'value' => '99%',
            'basis' => '',
            'effective_on' => '2026-09-01',
        ])->assertSessionHasErrors('basis');
    }

    public function test_a_new_standard_cannot_be_created_as_certified_without_evidence(): void
    {
        $this->seed();
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.compliance.store'), [
            'standard' => 'ISO/IEC 27001:2022',
            'status' => ComplianceStatus::Certified->value,
            'reviewed_on' => '2026-09-01',
            'evidence_url' => '',
        ])->assertSessionHasErrors('evidence_url');

        $this->assertDatabaseMissing('compliance_claims', ['standard' => 'ISO/IEC 27001:2022']);

        $this->actingAs($admin)->post(route('admin.compliance.store'), [
            'standard' => 'ISO/IEC 27001:2022',
            'status' => ComplianceStatus::Certified->value,
            'reviewed_on' => '2026-09-01',
            'evidence_url' => 'https://example.org/certificate.pdf',
        ])->assertSessionHasNoErrors();

        $claim = ComplianceClaim::query()->where('standard', 'ISO/IEC 27001:2022')->sole();

        $this->actingAs($admin)->delete(route('admin.compliance.destroy', $claim))->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('compliance_claims', ['id' => $claim->id]);
    }

    public function test_a_new_platform_is_created_undisclosed(): void
    {
        $this->seed();

        $this->actingAs($this->admin())->post(route('admin.platforms.store'), [
            'title' => 'Clearing gateway',
            'slug' => 'clearing-gateway',
            'system_context' => 'Routes settlement instructions between member banks.',
            'stack' => "PHP\nPostgreSQL",
        ])->assertSessionHasNoErrors();

        $platform = PlatformReference::query()->where('slug', 'clearing-gateway')->sole();

        $this->assertFalse($platform->cleared_for_disclosure, 'Scale figures must not disclose themselves.');
        $this->assertSame(['PHP', 'PostgreSQL'], $platform->stack);
        $this->assertSame([], $platform->scale_metrics);
    }

    public function test_a_duplicate_slug_is_refused_on_creation(): void
    {
        $this->seed();
        $existing = PlatformReference::query()->firstOrFail();

        $this->actingAs($this->admin())->post(route('admin.platforms.store'), [
            'title' => 'Another thing',
            'slug' => $existing->slug,
            'system_context' => 'Anything.',
        ])->assertSessionHasErrors('slug');
    }

    public function test_creating_and_deleting_are_closed_to_everyone_else(): void
    {
        $this->seed();
        $member = TeamMember::query()->firstOrFail();

        $this->post(route('admin.team.store'), [])->assertRedirect();
        $this->delete(route('admin.team.destroy', $member))->assertRedirect();

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->delete(route('admin.team.destroy', $member))
            ->assertForbidden();

        $this->assertDatabaseHas('team_members', ['id' => $member->id]);
    }
}
