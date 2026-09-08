<?php

namespace App\Erp\Support;

use App\Erp\Enums\ErpRole;
use App\Erp\Enums\ProjectRole;
use App\Erp\Enums\ProjectStatus;
use App\Erp\Enums\TaskStatus;
use App\Erp\Models\ActivityEntry;
use App\Erp\Models\Project;
use App\Erp\Models\Task;
use App\Erp\Models\TaskReview;
use App\Erp\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * A worked example of the division, for looking at before it is used in anger.
 *
 * The delivery system opens completely empty, which is the honest state but a
 * poor way to judge whether the workflow is the right one: an empty board shows
 * none of the gates, none of the review traffic and none of the progress
 * arithmetic. This plants one of each so the whole machine can be seen running,
 * with an account per post to sign in and try it from.
 *
 * Everything it creates is marked, and marked in the data rather than in a
 * convention somebody has to remember: accounts sit on the DEMO_DOMAIN and
 * projects carry the DEMO_PREFIX. remove() deletes exactly that set and can
 * touch nothing else, so the demonstration can be cleared out on the day the
 * first real engagement is opened.
 */
final class DemoData
{
    /** Marks an account as part of the demonstration set. Never routable mail. */
    public const DOMAIN = 'demo.jiranisokotech.co.ke';

    /** Marks a project as part of the demonstration set. */
    public const PREFIX = 'JTS-DEMO';

    /**
     * The one password every demonstration account shares.
     *
     * Deliberately not a secret: these are throwaway accounts on marked
     * addresses that hold no real work, and the point of them is to be handed
     * round and signed into. No real account is ever created here.
     */
    public const PASSWORD = 'Jiranisoko#Demo2026';

    public static function exists(): bool
    {
        return User::query()->where('email', 'like', '%@'.self::DOMAIN)->exists();
    }

    /** @return array<string, User> */
    public static function people(): array
    {
        return User::query()
            ->where('email', 'like', '%@'.self::DOMAIN)
            ->get()
            ->keyBy(fn (User $u) => str($u->email)->before('@')->value())
            ->all();
    }

    public static function plant(): void
    {
        DB::transaction(function (): void {
            $people = self::enrol();
            self::openEngagements($people);
        });

        FoundingPost::forget();
    }

    /**
     * @return array<string, User>
     */
    private static function enrol(): array
    {
        $password = Hash::make(self::PASSWORD);

        // Every post on /company/leadership, so each one can be signed into and
        // compared against what the page says it is accountable for.
        $roll = [
            'md' => ['Achieng Odhiambo', ErpRole::ManagingDirector, 'Managing Director'],
            'cto' => ['Brian Kiptoo', ErpRole::ChiefTechnologyOfficer, 'Chief Technology Officer'],
            'delivery' => ['Wanjiru Mwangi', ErpRole::DirectorOfDelivery, 'Director of Delivery'],
            'security' => ['Hassan Noor', ErpRole::HeadOfInformationSecurity, 'Head of Information Security'],
            'dpo' => ['Njeri Kamau', ErpRole::DataProtectionOfficer, 'Data Protection Officer'],

            'lead.ai' => ['Otieno Were', ErpRole::PracticeLead, 'Practice Lead — Artificial Intelligence & Automation'],
            'lead.payments' => ['Fatuma Ali', ErpRole::PracticeLead, 'Practice Lead — Financial Technology & Payments'],
            'lead.cloud' => ['Kipchoge Rotich', ErpRole::PracticeLead, 'Practice Lead — Cloud Infrastructure & DevOps'],

            'eng.mutai' => ['Dennis Mutai', ErpRole::Engineer, 'Senior Software Engineer'],
            'eng.chebet' => ['Sharon Chebet', ErpRole::Engineer, 'Software Engineer'],
            'eng.omondi' => ['Victor Omondi', ErpRole::Engineer, 'Senior Platform Engineer'],
            'eng.wairimu' => ['Grace Wairimu', ErpRole::Engineer, 'Software Engineer'],
            'eng.barasa' => ['Elvis Barasa', ErpRole::Engineer, 'Data Engineer'],
        ];

        $people = [];

        foreach ($roll as $handle => [$name, $role, $title]) {
            $people[$handle] = User::create([
                'name' => $name,
                'email' => $handle.'@'.self::DOMAIN,
                'password' => $password,
                'erp_role' => $role,
                'job_title' => $title,
                'is_active' => true,
                'is_admin' => false,
            ]);
        }

        return $people;
    }

    /**
     * @param  array<string, User>  $p
     */
    private static function openEngagements(array $p): void
    {
        // --- one live engagement, mid-flight -------------------------------
        $revenue = self::project([
            'code' => self::PREFIX.'-001',
            'name' => 'County revenue collection portal',
            'slug' => 'demo-county-revenue-portal',
            'client' => 'Uasin Gishu County Government',
            'status' => ProjectStatus::Active,
            'summary' => 'Single-till collection for parking, business permits and market '
                .'fees, reconciled daily against the county bank account. Replaces four '
                .'separate cash points and the spreadsheets behind them.',
            'repository_url' => 'https://github.com/jiranisoko/county-revenue',
            'default_branch' => 'main',
            'started_on' => now()->subWeeks(7)->toDateString(),
            'target_date' => now()->addWeeks(5)->toDateString(),
        ], $p['delivery']);

        $revenue->members()->attach($p['lead.payments']->id, ['role' => ProjectRole::Lead->value]);
        $revenue->members()->attach($p['eng.mutai']->id, ['role' => ProjectRole::Engineer->value]);
        $revenue->members()->attach($p['eng.chebet']->id, ['role' => ProjectRole::Engineer->value]);
        $revenue->members()->attach($p['security']->id, ['role' => ProjectRole::Reviewer->value]);
        $revenue->members()->attach($p['dpo']->id, ['role' => ProjectRole::Observer->value]);

        self::task($revenue, 1, [
            'title' => 'Till number provisioning and callback verification',
            'status' => TaskStatus::Done, 'weight' => 5, 'assignee' => $p['eng.mutai'],
            'author' => $p['lead.payments'], 'days' => 34,
            'documentation' => 'Provisioning runs against the sandbox short code. Callbacks are '
                .'verified by checksum before the ledger is touched, so a replayed callback cannot '
                ."post twice.\n\nVerify: post the same callback body twice and confirm one ledger row.",
            'branch' => 'feature/demo-001-till-provisioning',
            'merge_reference' => '7f2c1ab',
            'approved_by' => $p['lead.payments'], 'approval' => 'Checksum path is right. Merged.',
        ]);

        self::task($revenue, 2, [
            'title' => 'Daily settlement reconciliation job',
            'status' => TaskStatus::Done, 'weight' => 8, 'assignee' => $p['eng.chebet'],
            'author' => $p['lead.payments'], 'days' => 21,
            'documentation' => 'Nightly job matches gateway settlements to ledger postings and '
                .'writes an exceptions report for anything unmatched after two runs.',
            'branch' => 'feature/demo-001-reconciliation',
            'merge_reference' => 'c94e07d',
            'approved_by' => $p['delivery'], 'approval' => 'Exceptions report is what the county asked for.',
        ]);

        // The review queue the lead opens on.
        self::task($revenue, 3, [
            'title' => 'Market fee receipts by SMS',
            'status' => TaskStatus::InReview, 'weight' => 3, 'assignee' => $p['eng.mutai'],
            'author' => $p['lead.payments'], 'days' => 4, 'submitted' => 1,
            'documentation' => 'Receipt is sent on ledger commit, not on callback receipt, so a '
                ."trader is never told they have paid before the money is recorded.\n\n"
                .'Verify: force a ledger failure and confirm no SMS leaves.',
            'branch' => 'feature/demo-001-sms-receipts',
        ]);

        // Something the security post is holding, to show the independent gate.
        self::task($revenue, 4, [
            'title' => 'Operator session handling on shared terminals',
            'status' => TaskStatus::ChangesRequested, 'weight' => 5, 'assignee' => $p['eng.chebet'],
            'author' => $p['lead.payments'], 'days' => 6, 'submitted' => 3,
            'documentation' => 'Idle timeout plus explicit hand-over between operators on one terminal.',
            'branch' => 'feature/demo-001-operator-sessions',
            'changes_by' => $p['security'],
            'changes' => 'Timeout is right but the hand-over reuses the session identifier. '
                .'Rotate it, otherwise the outgoing operator can act as the incoming one.',
        ]);

        self::task($revenue, 5, [
            'title' => 'Business permit renewal flow',
            'status' => TaskStatus::InProgress, 'weight' => 8, 'assignee' => $p['eng.chebet'],
            'author' => $p['lead.payments'], 'days' => 3, 'due' => 9,
        ]);

        self::task($revenue, 6, [
            'title' => 'Bank statement import for manual matching',
            'status' => TaskStatus::Blocked, 'weight' => 3, 'assignee' => $p['eng.mutai'],
            'author' => $p['lead.payments'], 'days' => 11, 'due' => -2,
            'blocked_reason' => 'Waiting on the county treasury for a sample statement export. '
                .'Chased 3 October; no file yet.',
        ]);

        self::task($revenue, 7, [
            'title' => 'Parking enforcement handheld app',
            'status' => TaskStatus::Backlog, 'weight' => 13, 'assignee' => null,
            'author' => $p['lead.payments'], 'days' => 2,
        ]);

        self::task($revenue, 8, [
            'title' => 'USSD fallback for feature phones',
            'status' => TaskStatus::Cancelled, 'weight' => 8, 'assignee' => null,
            'author' => $p['delivery'], 'days' => 15,
        ]);

        // --- one engagement waiting on a decision --------------------------
        $sacco = self::project([
            'code' => self::PREFIX.'-002',
            'name' => 'Sacco core banking migration',
            'slug' => 'demo-sacco-core-migration',
            'client' => 'Rift Valley Teachers Sacco Society Ltd',
            'status' => ProjectStatus::OnHold,
            'summary' => 'Migration from a vendor core to an in-house ledger, member by member, '
                .'with dual running until the balances agree for three consecutive month-ends.',
            'repository_url' => 'https://github.com/jiranisoko/sacco-core',
            'default_branch' => 'main',
            'started_on' => now()->subWeeks(14)->toDateString(),
            'target_date' => now()->addWeeks(16)->toDateString(),
        ], $p['md']);

        $sacco->members()->attach($p['lead.cloud']->id, ['role' => ProjectRole::Lead->value]);
        $sacco->members()->attach($p['eng.omondi']->id, ['role' => ProjectRole::Engineer->value]);
        $sacco->members()->attach($p['eng.barasa']->id, ['role' => ProjectRole::Engineer->value]);
        $sacco->members()->attach($p['cto']->id, ['role' => ProjectRole::Reviewer->value]);

        self::task($sacco, 1, [
            'title' => 'Member and account extract from the vendor core',
            'status' => TaskStatus::Done, 'weight' => 8, 'assignee' => $p['eng.barasa'],
            'author' => $p['lead.cloud'], 'days' => 60,
            'documentation' => 'Extract runs read-only against a restored backup, never the live core.',
            'branch' => 'feature/demo-002-extract', 'merge_reference' => 'a11d3f9',
            'approved_by' => $p['cto'], 'approval' => 'Read-only against a restore is the right call.',
        ]);

        self::task($sacco, 2, [
            'title' => 'Dual-running balance comparison report',
            'status' => TaskStatus::Approved, 'weight' => 5, 'assignee' => $p['eng.omondi'],
            'author' => $p['lead.cloud'], 'days' => 9, 'submitted' => 5,
            'documentation' => 'Compares closing balances per member per day and lists only the drift.',
            'branch' => 'feature/demo-002-dual-running',
            'approved_by' => $p['cto'], 'approval' => 'Approved. Hold the merge until the migration restarts.',
        ]);

        self::task($sacco, 3, [
            'title' => 'Interest accrual parity with the vendor core',
            'status' => TaskStatus::Blocked, 'weight' => 13, 'assignee' => $p['eng.omondi'],
            'author' => $p['lead.cloud'], 'days' => 20, 'due' => -12,
            'blocked_reason' => 'Sacco board paused the migration pending their AGM. '
                .'Nothing to do here until the engagement resumes.',
        ]);

        self::task($sacco, 4, [
            'title' => 'Member statement generation',
            'status' => TaskStatus::Backlog, 'weight' => 8, 'assignee' => null,
            'author' => $p['lead.cloud'], 'days' => 18,
        ]);

        // --- one still being scoped ----------------------------------------
        $records = self::project([
            'code' => self::PREFIX.'-003',
            'name' => 'Clinic records interoperability',
            'slug' => 'demo-clinic-records',
            'client' => 'Nakuru Family Health Group',
            'status' => ProjectStatus::Discovery,
            'summary' => 'Shared patient record across eleven clinics, on the ministry data '
                .'standard, with consent recorded per disclosure rather than once at sign-up.',
            'default_branch' => 'main',
            'started_on' => now()->subWeeks(2)->toDateString(),
            'target_date' => now()->addWeeks(3)->toDateString(),
        ], $p['cto']);

        $records->members()->attach($p['lead.ai']->id, ['role' => ProjectRole::Lead->value]);
        $records->members()->attach($p['eng.wairimu']->id, ['role' => ProjectRole::Engineer->value]);
        $records->members()->attach($p['dpo']->id, ['role' => ProjectRole::Reviewer->value]);

        self::task($records, 1, [
            'title' => 'Data protection impact assessment',
            'status' => TaskStatus::InReview, 'weight' => 5, 'assignee' => $p['eng.wairimu'],
            'author' => $p['lead.ai'], 'days' => 5, 'submitted' => 2,
            'documentation' => 'Lawful basis, retention and the consent record per disclosure. '
                .'Filed with the Data Protection Officer before any clinic data is copied.',
            'branch' => 'feature/demo-003-dpia',
        ]);

        self::task($records, 2, [
            'title' => 'Clinic system inventory and interface survey',
            'status' => TaskStatus::InProgress, 'weight' => 3, 'assignee' => $p['eng.wairimu'],
            'author' => $p['lead.ai'], 'days' => 6, 'due' => 4,
        ]);

        self::task($records, 3, [
            'title' => 'Consent model and disclosure log design',
            'status' => TaskStatus::Backlog, 'weight' => 8, 'assignee' => null,
            'author' => $p['lead.ai'], 'days' => 4,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function project(array $attributes, User $opener): Project
    {
        $project = Project::create($attributes);

        ActivityEntry::record($project, 'created', $opener);

        return $project;
    }

    /**
     * @param  array<string, mixed>  $spec
     */
    private static function task(Project $project, int $n, array $spec): Task
    {
        /** @var TaskStatus $status */
        $status = $spec['status'];
        $age = $spec['days'] ?? 5;

        $task = $project->tasks()->create([
            'reference' => sprintf('%s-%03d', $project->code, $n),
            'title' => $spec['title'],
            'description' => $spec['description'] ?? null,
            'assignee_id' => ($spec['assignee'] ?? null)?->id,
            'created_by' => $spec['author']->id,
            'status' => $status,
            'weight' => $spec['weight'],
            'documentation' => $spec['documentation'] ?? null,
            'branch' => $spec['branch'] ?? null,
            'merge_reference' => $spec['merge_reference'] ?? null,
            'blocked_reason' => $spec['blocked_reason'] ?? null,
            'due_on' => isset($spec['due']) ? now()->addDays($spec['due'])->toDateString() : null,
            'started_at' => $status === TaskStatus::Backlog ? null : now()->subDays($age),
            'submitted_at' => isset($spec['submitted']) ? now()->subDays($spec['submitted']) : null,
            'completed_at' => $status === TaskStatus::Done ? now()->subDays(max(1, (int) ($age / 3))) : null,
        ]);

        ActivityEntry::record($task, 'created', $spec['author']);

        if (isset($spec['assignee'])) {
            ActivityEntry::record($task, 'transitioned', $spec['assignee'],
                TaskStatus::Backlog->value, TaskStatus::InProgress->value);
        }

        if (isset($spec['submitted']) || $status === TaskStatus::Done) {
            ActivityEntry::record($task, 'transitioned', $spec['assignee'],
                TaskStatus::InProgress->value, TaskStatus::InReview->value);
        }

        if (isset($spec['approved_by'])) {
            TaskReview::create([
                'task_id' => $task->id,
                'reviewer_id' => $spec['approved_by']->id,
                'decision' => TaskReview::APPROVED,
                'notes' => $spec['approval'] ?? null,
            ]);

            ActivityEntry::record($task, 'reviewed', $spec['approved_by'], null, null, $spec['approval'] ?? null);
        }

        if (isset($spec['changes_by'])) {
            TaskReview::create([
                'task_id' => $task->id,
                'reviewer_id' => $spec['changes_by']->id,
                'decision' => TaskReview::CHANGES_REQUESTED,
                'notes' => $spec['changes'],
            ]);

            ActivityEntry::record($task, 'reviewed', $spec['changes_by'], null, null, $spec['changes']);

            TaskUpdate::create([
                'task_id' => $task->id,
                'user_id' => $spec['assignee']->id,
                'body' => 'Understood — rotating the identifier on hand-over and adding a test '
                    .'that the old one stops working. Back to you today.',
            ]);
        }

        if ($status === TaskStatus::Done) {
            ActivityEntry::record($task, 'transitioned', $spec['approved_by'] ?? $spec['author'],
                TaskStatus::Approved->value, TaskStatus::Done->value, $spec['merge_reference'] ?? null);
        }

        return $task;
    }

    /**
     * Removes exactly what plant() created, and nothing else.
     *
     * The activity trail is append-only and its model refuses deletion, which is
     * the point of it: nobody edits the record of what happened. This deletes at
     * the query level instead, and only rows whose subject is a demonstration
     * project or task. That is not a hole in the audit trail — none of these rows
     * recorded real work — but it is the one place in the application allowed to
     * do it, which is why it is here and not a general capability.
     */
    public static function remove(): void
    {
        DB::transaction(function (): void {
            $projects = Project::query()->where('code', 'like', self::PREFIX.'-%')->get();
            $taskIds = Task::query()->whereIn('project_id', $projects->modelKeys())->pluck('id');
            $userIds = User::query()->where('email', 'like', '%@'.self::DOMAIN)->pluck('id');

            DB::table('activity_entries')
                ->where(fn ($q) => $q
                    ->where(fn ($s) => $s->where('subject_type', Task::class)->whereIn('subject_id', $taskIds))
                    ->orWhere(fn ($s) => $s->where('subject_type', Project::class)->whereIn('subject_id', $projects->modelKeys()))
                )
                ->delete();

            DB::table('activity_entries')->whereIn('user_id', $userIds)->delete();

            // project_members, tasks, task_reviews and task_updates all cascade.
            Project::query()->whereIn('id', $projects->modelKeys())->delete();
            User::query()->whereIn('id', $userIds)->delete();
        });

        // Mass deletes fire no model events, so the founding post has to be told
        // that the division is empty again.
        FoundingPost::forget();
    }
}
