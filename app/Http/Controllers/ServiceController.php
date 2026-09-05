<?php

namespace App\Http\Controllers;

use App\Models\Pillar;
use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Template T-04, the modular core service page.
     */
    public function show(Pillar $pillar, Service $service): View
    {
        return view('services.show', [
            'pillar' => $pillar,
            'service' => $service,
            'related' => $pillar->services()->ordered()->whereKeyNot($service->getKey())->get(),
        ]);
    }
}
