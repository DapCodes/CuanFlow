<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpportunityMapController extends Controller
{
    public function index()
    {
        return view('opportunity.index');
    }
}
