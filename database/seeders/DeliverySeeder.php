<?php

namespace Database\Seeders;

use App\Erp\Enums\ErpRole;
use App\Erp\Enums\ProjectRole;
use App\Erp\Enums\ProjectStatus;
use App\Erp\Enums\TaskStatus;
use App\Erp\Models\ActivityEntry;
use App\Erp\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Opens the delivery system with a shape rather than with data.
 *
 * One project carrying the group's own marketplace work, because that engagement
 * is real, and a small set of tasks spread across the workflow so that a lead
 * signing in for the first time sees what each state looks like instead of an
 * empty board they have to imagine their way into.
 *
 * Not run in production by default — see DatabaseSeeder.
 */
class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('ChangeMe!2026#JTS');

        $director = User::query()->firstOrCreate(
            ['email' => 'md@jiranisokotech.co.ke'],
            [
                'name' => 'Managing Director',
                'password' => $password,
                'erp_role' => ErpRole::ManagingDirector,
                'job_title' => 'Managing Director',
                'is_active' => true,
            ],
        );

        $lead = User::query()->firstOrCreate(
            ['email' => 'lead.payments@jiranisokotech.co.ke'],
            [
                'name' => 'Payments Practice Lead',
                'password' => $password,
                'erp_role' => ErpRole::PracticeLead,
                'job_title' => 'Practice Lead — FinTech & Payments',
                'is_active' => true,
            ],
        );

        $engineer = User::query()->firstOrCreate(
            ['email' => 'engineer.one@jiranisokotech.co.ke'],
            [
                'name' => 'Senior Engineer',
                'password' => $password,
                'erp_role' => ErpRole::Engineer,
                'job_title' => 'Senior Engineer',
                'is_active' => true,
            ],
        );

        if (Project::query()->exists()) {
            return;
        }

        $project = Project::create([
            'code' => 'JTS-P-001',
            'name' => 'Marketplace payment rails',
            'slug' => 'marketplace-payment-rails',
            'client' => 'Jiranisoko Market Ltd (group)',
            'status' => ProjectStatus::Active,
            'summary' => 'Reconciliation and settlement for the group marketplace: '
                .'gateway integration, ledger postings and an audit trail the finance '
                .'function can be examined on.',
            'repository_url' => 'https://github.com/meshtirop1/jiranitech',
            'default_branch' => 'main',
            'started_on' => now()->subWeeks(3)->toDateString(),
            'target_date' => now()->addWeeks(9)->toDateString(),
        ]);

        $project->members()->attach($lead->id, ['role' => ProjectRole::Lead->value]);
        $project->members()->attach($engineer->id, ['role' => ProjectRole::Engineer->value]);
        $project->members()->attach($director->id, ['role' => ProjectRole::Reviewer->value]);

        // One task in each meaningful state, so every part of the board is
        // demonstrated on first sign-in.
        $rows = [
            ['Settlement ledger schema', TaskStatus::Done, 5, $engineer, true],
            ['Gateway sandbox integration', TaskStatus::InReview, 3, $engineer, true],
            ['Reconciliation job and retry policy', TaskStatus::InProgress, 5, $engineer, false],
            ['Refund and chargeback flow', TaskStatus::Backlog, 8, null, false],
            ['Provider rate-limit handling', TaskStatus::Blocked, 2, $engineer, false],
        ];

        foreach ($rows as $i => [$title, $status, $weight, $assignee, $documented]) {
            $task = $project->tasks()->create([
                'reference' => sprintf('JTS-P-001-%03d', $i + 1),
                'title' => $title,
                'description' => 'Seeded to demonstrate the '.$status->label().' state.',
                'assignee_id' => $assignee?->id,
                'created_by' => $lead->id,
                'status' => $status,
                'weight' => $weight,
                'documentation' => $documented
                    ? "Approach, decisions taken and how to verify the change.\n\nReplace this with the real record."
                    : null,
                'branch' => $documented ? 'feature/jts-p-001-'.($i + 1) : null,
                'blocked_reason' => $status === TaskStatus::Blocked
                    ? 'Waiting on sandbox credentials from the provider.'
                    : null,
                'started_at' => $status === TaskStatus::Backlog ? null : now()->subDays(6 - $i),
                'submitted_at' => $status === TaskStatus::InReview ? now()->subDay() : null,
                'completed_at' => $status === TaskStatus::Done ? now()->subDays(2) : null,
                'due_on' => now()->addDays(($i + 1) * 5)->toDateString(),
            ]);

            ActivityEntry::record($task, 'created', $lead);
        }

        ActivityEntry::record($project, 'created', $director);
    }
}
