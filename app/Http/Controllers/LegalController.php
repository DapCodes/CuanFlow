<?php

namespace App\Http\Controllers;

use App\Models\TermsAndCondition;

class LegalController extends Controller
{
    public function terms()
    {
        $terms = TermsAndCondition::first();

        return view('legal.terms', compact('terms'));
    }
}
