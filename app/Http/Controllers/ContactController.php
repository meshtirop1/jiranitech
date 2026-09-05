<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact.index');
    }

    public function engagementDesk(): View
    {
        return view('contact.engagement-desk');
    }
}
