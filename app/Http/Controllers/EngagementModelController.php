<?php

namespace App\Http\Controllers;

use App\Models\EngagementModel;
use Illuminate\View\View;

class EngagementModelController extends Controller
{
    public function index(): View
    {
        return view('engagement-models.index', [
            'engagementModels' => EngagementModel::query()->ordered()->get(),
        ]);
    }

    public function show(EngagementModel $engagementModel): View
    {
        return view('engagement-models.show', [
            'engagementModel' => $engagementModel,
            'siblings' => EngagementModel::query()->ordered()->whereKeyNot($engagementModel->getKey())->get(),
        ]);
    }
}
