<?php

namespace App\Http\Controllers\Erp;

use App\Erp\Enums\TaskStatus;
use App\Erp\Models\Project;
use App\Erp\Models\Task;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * What the system opens on.
 *
 * Ordered by whose move it is rather than by date: an engineer needs to see the
 * work waiting on them, and a lead needs to see what they are holding up. A feed
 * of everything that happened is a different, less useful thing.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $projects = Project::query()
            ->visibleTo($user)
            ->with(['tasks', 'members'])
            ->orderByRaw("CASE status WHEN 'active' THEN 1 WHEN 'discovery' THEN 2 WHEN 'on_hold' THEN 3 ELSE 4 END")
            ->orderBy('name')
            ->get();

        $running = $projects->filter(fn (Project $p) => $p->status->isRunning());

        return view('erp.dashboard', [
            'user' => $user,
            'myWork' => $user->openWork(),
            'reviewQueue' => $user->reviewQueue(),
            'projects' => $projects,
            'blocked' => Task::query()
                ->whereIn('project_id', $projects->modelKeys())
                ->where('status', TaskStatus::Blocked->value)
                ->with(['project', 'assignee'])
                ->get(),
            'overdue' => Task::query()
                ->whereIn('project_id', $projects->modelKeys())
                ->open()
                ->whereNotNull('due_on')
                ->whereDate('due_on', '<', today())
                ->with(['project', 'assignee'])
                ->orderBy('due_on')
                ->get(),
            'runningCount' => $running->count(),
        ]);
    }
}
