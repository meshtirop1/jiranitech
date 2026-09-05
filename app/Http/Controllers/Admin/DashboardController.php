<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insight;
use App\Models\JobOpening;
use App\Models\Pillar;
use App\Models\RfpSubmission;
use App\Models\Service;
use App\Models\TeamMember;
use App\Support\PublicationGates;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'gates' => PublicationGates::all(),
            'openGates' => PublicationGates::openCount(),
            'blockingGates' => PublicationGates::blockingCount(),
            'counts' => [
                'pillars' => Pillar::query()->count(),
                'services' => Service::query()->count(),
                'insights' => Insight::query()->published()->count(),
                'team' => TeamMember::query()->count(),
                'jobs' => JobOpening::query()->where('is_published', true)->count(),
                'rfp' => RfpSubmission::query()->count(),
            ],
            'recentRfp' => RfpSubmission::query()->latest()->take(5)->get(),
        ]);
    }
}
