<?php

namespace Tests\Feature;

use App\Erp\Enums\ErpRole;
use App\Erp\Models\Project;
use App\Erp\Models\Task;
use App\Models\User;
use Database\Seeders\DeliverySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Renders the delivery pages as each kind of user and writes them out for a
 * visual check. Runs only when ERP_SNAPSHOT_DIR is set, so it is inert in a
 * normal test run and in CI.
 */
class ErpRenderSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_snapshot(): void
    {
        $dir = env('ERP_SNAPSHOT_DIR');

        if (blank($dir)) {
            $this->markTestSkipped('ERP_SNAPSHOT_DIR not set.');
        }

        $this->seed(DeliverySeeder::class);

        $lead = User::query()->where('erp_role', ErpRole::Lead->value)->sole();
        $project = Project::query()->firstOrFail();
        $task = Task::query()->where('status', 'in_review')->firstOrFail();

        $pages = [
            'dashboard' => '/erp',
            'projects' => '/erp/projects',
            'project' => '/erp/projects/'.$project->slug,
            'task' => '/erp/tasks/'.$task->reference,
            'reviews' => '/erp/reviews',
        ];

        @mkdir($dir, 0775, true);

        foreach ($pages as $name => $path) {
            $html = $this->actingAs($lead)->get($path)->assertOk()->getContent();
            file_put_contents($dir.'/'.$name.'.html', $html);
        }

        $this->assertFileExists($dir.'/dashboard.html');
    }
}
