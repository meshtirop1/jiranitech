<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalController extends Controller
{
    public function privacy(): View
    {
        return view('legal.privacy-notice');
    }

    public function terms(): View
    {
        return view('legal.terms-of-engagement');
    }

    public function dataProcessing(): View
    {
        return view('legal.data-processing-addendum');
    }

    public function disclosure(): View
    {
        return view('legal.responsible-disclosure');
    }

    public function accessibility(): View
    {
        return view('legal.accessibility-statement');
    }
}
