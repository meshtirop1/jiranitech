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

    /**
     * Annex 3 to the addendum, published separately so that a change of
     * sub-processor can be notified without reopening a signed instrument.
     */
    public function subProcessors(): View
    {
        return view('legal.sub-processors');
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
