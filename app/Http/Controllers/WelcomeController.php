<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Display the main landing page.
     */
    public function index()
    {
        // Fetch total user count
        $totalUsersCount = User::count();

        // Fetch latest 5 owners for the avatar widget
        $latestOwners = User::role('owner')
            ->latest()
            ->take(5)
            ->get(['name', 'avatar', 'google_id', 'google_avatar']);

        return view('landingpage.index', compact('totalUsersCount', 'latestOwners'));
    }
}
