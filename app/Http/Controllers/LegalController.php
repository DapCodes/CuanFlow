<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function terms()
    {
        $terms = \App\Models\TermsAndCondition::first();

        return view('legal.terms', compact('terms'));
    }
}
