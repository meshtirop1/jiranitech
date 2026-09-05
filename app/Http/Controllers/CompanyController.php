<?php

namespace App\Http\Controllers;

use App\Enums\SlaTier;
use App\Models\ComplianceClaim;
use App\Models\JobOpening;
use App\Models\Pillar;
use App\Models\TeamMember;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        return view('company.index');
    }

    public function about(): View
    {
        return view('company.about');
    }

    public function governance(): View
    {
        return view('company.governance', [
            'claims' => ComplianceClaim::query()->ordered()->get(),
            'tiers' => SlaTier::cases(),
        ]);
    }

    public function leadership(): View
    {
        $leadership = TeamMember::query()->published()->ordered()->get();

        return view('company.leadership', [
            'leadership' => $leadership,
            'pendingAppointments' => $leadership->reject->isAnnounced()->count(),
            'pillarsBySlug' => Pillar::query()->ordered()->get()->keyBy('slug'),
        ]);
    }

    public function deliveryModel(): View
    {
        return view('company.delivery-model', [
            'tiers' => SlaTier::cases(),
        ]);
    }

    public function careers(): View
    {
        return view('company.careers', [
            'openings' => JobOpening::query()->published()->ordered()->get(),
            'draftCount' => JobOpening::query()->where('is_published', false)->count(),
            'pillarsBySlug' => Pillar::query()->ordered()->get()->keyBy('slug'),
        ]);
    }
}
