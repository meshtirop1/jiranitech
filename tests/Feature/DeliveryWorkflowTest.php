<?php

namespace Tests\Feature;

use App\Erp\Enums\ErpRole;
use App\Erp\Enums\ProjectRole;
use App\Erp\Enums\TaskStatus;
use App\Erp\Models\ActivityEntry;
use App\Erp\Models\Project;
use App\Erp\Models\Task;
use App\Erp\Support\TaskTransition;
use App\Erp\Support\TaskWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * The rules that make this a delivery system rather than a shared task list.
 *
 * Each of these is a control somebody will eventually want to route around at
 * 6pm on a Friday, which is exactly why they are asserted rather than trusted.
 */
class DeliveryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $director;

    private User $lead;

    private User $engineer;

    private User $outsider;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->director = User::factory()->create(['erp_role' => ErpRole::DirectorOfDelivery, 'is_active' => true]);
        $this->lead = User::factory()->create(['erp_role' => ErpRole::PracticeLead, 'is_active' => true]);
        $this->engineer = User::factory()->create(['erp_role' => ErpRole::Engineer, 'is_active' => true]);
        $this->outsider = User::factory()->create(['erp_role' => ErpRole::Engineer, 'is_active' => true]);

        $this->project = Project::create([
            'code' => 'JTS-P-900', 'name' => 'Test engagement', 'slug' => 'test-engagement',
            'status' => 'active', 'default_branch' => 'main',
        ]);

        $this->project->members()->attach($this->lead->id, ['role' => ProjectRole::Lead->value]);
        $this->project->members()->attach($this->engineer->id, ['role' => ProjectRole::Engineer->value]);
    }

    private function task(array $attributes = []): Task
    {
        return $this->project->tasks()->create([
            'reference' => 'JTS-P-900-'.str_pad((string) (Task::count() + 1), 3, '0', STR_PAD_LEFT),
            'title' => 'A unit of work',
            'assignee_id' => $this->engineer->id,
            'created_by' => $this->lead->id,
            'status' => TaskStatus::InProgress,
            'weight' => 1,
            ...$attributes,
        ]);
    }

    private function ready(array $attributes = []): Task
    {
        return $this->task([
            'documentation' => 'What was done, and why.',
            'branch' => 'feature/jts-p-900-1',
            ...$attributes,
        ]);
    }

    // --- documentation is a gate --------------------------------------------

    public function test_work_cannot_go_to_review_without_documentation(): void
    {
        $task = $this->task(['branch' => 'feature/x']);

        $refusal = TaskWorkflow::refusal($task, TaskStatus::InReview, $this->engineer);

        $this->assertNotNull($refusal);
        $this->assertStringContainsString('documentation', strtolower($refusal));
    }

    public function test_work_cannot_go_to_review_without_a_branch(): void
    {
        $task = $this->task(['documentation' => 'Written up.']);

        $this->assertStringContainsString(
            'branch',
            strtolower((string) TaskWorkflow::refusal($task, TaskStatus::InReview, $this->engineer)),
        );
    }

    public function test_a_documented_task_may_be_submitted_by_its_assignee(): void
    {
        $task = $this->ready();

        TaskTransition::apply($task, TaskStatus::InReview, $this->engineer);

        $this->assertSame(TaskStatus::InReview, $task->fresh()->status);
        $this->assertNotNull($task->fresh()->submitted_at);
    }

    public function test_only_the_assignee_submits_their_own_work(): void
    {
        $task = $this->ready();

        $this->assertStringContainsString(
            'assignee',
            strtolower((string) TaskWorkflow::refusal($task, TaskStatus::InReview, $this->lead)),
        );
    }

    // --- nobody reviews their own work --------------------------------------

    public function test_a_lead_cannot_approve_their_own_work(): void
    {
        // The lead is also the assignee here, which is the case that matters:
        // on a small team the reviewer and the author are often the same person.
        $task = $this->ready(['assignee_id' => $this->lead->id, 'status' => TaskStatus::InReview]);

        $refusal = TaskWorkflow::refusal($task, TaskStatus::Approved, $this->lead);

        $this->assertNotNull($refusal);
        $this->assertStringContainsString('your own work', strtolower($refusal));
    }

    public function test_the_director_can_review_what_the_lead_wrote(): void
    {
        $task = $this->ready(['assignee_id' => $this->lead->id, 'status' => TaskStatus::InReview]);

        TaskTransition::apply($task, TaskStatus::Approved, $this->director, 'Read it, happy.');

        $this->assertSame(TaskStatus::Approved, $task->fresh()->status);
    }

    public function test_an_engineer_cannot_approve_anything(): void
    {
        $task = $this->ready(['status' => TaskStatus::InReview, 'assignee_id' => $this->lead->id]);

        $this->assertStringContainsString(
            'reviewer',
            strtolower((string) TaskWorkflow::refusal($task, TaskStatus::Approved, $this->engineer)),
        );
    }

    public function test_the_lead_reviews_and_accepts_an_engineers_work(): void
    {
        $task = $this->ready(['status' => TaskStatus::InReview]);

        TaskTransition::apply($task, TaskStatus::Approved, $this->lead, 'Looks right.');

        $task->refresh();
        $this->assertSame(TaskStatus::Approved, $task->status);
        $this->assertTrue($task->latestReview()->approved());
        $this->assertSame($this->lead->id, $task->latestReview()->reviewer_id);
    }

    public function test_requesting_changes_sends_it_back_with_the_reason_recorded(): void
    {
        $task = $this->ready(['status' => TaskStatus::InReview]);

        TaskTransition::apply($task, TaskStatus::ChangesRequested, $this->lead, 'No test for the retry path.');

        $task->refresh();
        $this->assertSame(TaskStatus::ChangesRequested, $task->status);
        $this->assertSame('No test for the retry path.', $task->latestReview()->notes);
        $this->assertFalse($task->latestReview()->approved());
    }

    // --- the rest of the machine --------------------------------------------

    public function test_a_task_cannot_skip_review_and_go_straight_to_done(): void
    {
        $task = $this->ready();

        $this->assertStringContainsString(
            'cannot move straight to',
            strtolower((string) TaskWorkflow::refusal($task, TaskStatus::Done, $this->lead)),
        );
    }

    public function test_blocking_requires_a_reason(): void
    {
        $task = $this->task();

        $this->assertStringContainsString(
            'blocking it',
            strtolower((string) TaskWorkflow::refusal($task, TaskStatus::Blocked, $this->engineer)),
        );

        $task->update(['blocked_reason' => 'Waiting on provider credentials.']);

        $this->assertTrue(TaskWorkflow::allows($task, TaskStatus::Blocked, $this->engineer));
    }

    public function test_recording_the_merge_closes_the_task_and_stores_the_reference(): void
    {
        $task = $this->ready(['status' => TaskStatus::Approved]);

        TaskTransition::apply($task, TaskStatus::Done, $this->lead, 'a1b2c3d');

        $task->refresh();
        $this->assertSame(TaskStatus::Done, $task->status);
        $this->assertSame('a1b2c3d', $task->merge_reference);
        $this->assertNotNull($task->completed_at);
    }

    public function test_a_refused_transition_throws_and_changes_nothing(): void
    {
        $task = $this->task();

        try {
            TaskTransition::apply($task, TaskStatus::InReview, $this->engineer);
            $this->fail('The workflow should have refused an undocumented submission.');
        } catch (RuntimeException) {
            // expected
        }

        $this->assertSame(TaskStatus::InProgress, $task->fresh()->status);
    }

    // --- the trail -----------------------------------------------------------

    public function test_every_transition_is_recorded_against_the_task(): void
    {
        $task = $this->ready();

        TaskTransition::apply($task, TaskStatus::InReview, $this->engineer);
        TaskTransition::apply($task, TaskStatus::Approved, $this->lead, 'Fine.');

        $trail = ActivityEntry::query()
            ->where('subject_type', Task::class)
            ->where('subject_id', $task->id)
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $trail);
        $this->assertSame('in_progress', $trail[0]->from_state);
        $this->assertSame('in_review', $trail[0]->to_state);
        $this->assertSame($this->engineer->id, $trail[0]->user_id);
        $this->assertSame($this->lead->id, $trail[1]->user_id);
    }

    public function test_the_trail_cannot_be_edited_or_deleted(): void
    {
        $task = $this->ready();
        TaskTransition::apply($task, TaskStatus::InReview, $this->engineer);

        $entry = ActivityEntry::query()->latest('id')->firstOrFail();

        $this->expectException(RuntimeException::class);
        $entry->update(['note' => 'tidied']);
    }

    public function test_the_trail_refuses_deletion(): void
    {
        $task = $this->ready();
        TaskTransition::apply($task, TaskStatus::InReview, $this->engineer);

        $this->expectException(RuntimeException::class);
        ActivityEntry::query()->latest('id')->firstOrFail()->delete();
    }

    // --- progress ------------------------------------------------------------

    public function test_progress_is_weighted_and_ignores_cancelled_work(): void
    {
        $this->task(['status' => TaskStatus::Done, 'weight' => 3]);
        $this->task(['status' => TaskStatus::InProgress, 'weight' => 1]);
        $this->task(['status' => TaskStatus::Cancelled, 'weight' => 8]);

        $progress = $this->project->fresh()->progress();

        // 3 of 4 points; the cancelled 8 never counted, because it was not owed.
        $this->assertSame(3, $progress['done']);
        $this->assertSame(4, $progress['total']);
        $this->assertSame(75, $progress['percent']);
    }

    public function test_a_project_with_no_work_reports_zero_rather_than_dividing_by_zero(): void
    {
        $this->assertSame(0, $this->project->progress()['percent']);
    }

    // --- visibility ----------------------------------------------------------

    public function test_an_engineer_sees_only_their_own_projects(): void
    {
        $this->assertTrue($this->project->mayBeSeenBy($this->engineer));
        $this->assertFalse($this->project->mayBeSeenBy($this->outsider));
    }

    public function test_the_director_sees_every_project_without_being_a_member(): void
    {
        $this->assertTrue($this->project->mayBeSeenBy($this->director));
        $this->assertNull($this->project->roleOf($this->director));
    }

    public function test_the_review_queue_never_contains_your_own_work(): void
    {
        $this->ready(['status' => TaskStatus::InReview, 'assignee_id' => $this->lead->id]);
        $mine = $this->ready(['status' => TaskStatus::InReview, 'assignee_id' => $this->engineer->id]);

        $queue = $this->lead->reviewQueue();

        $this->assertTrue($queue->contains('id', $mine->id));
        $this->assertCount(1, $queue, 'The lead should not be queued to review their own submission.');
    }
}
