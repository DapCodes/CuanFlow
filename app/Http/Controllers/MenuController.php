<?php

namespace App\Http\Controllers;

use App\Models\AiInsight;
use Illuminate\Http\Request;

class MenuController extends Controller // Atau HomeController tergantung route Anda
{
    public function index()
    {
        $user = auth()->user();
        
        // Ambil unread insights untuk user outlet ini
        $unreadInsights = AiInsight::where('outlet_id', $user->outlet_id)
            ->unread()
            ->active()
            ->orderBy('severity', 'desc') // Critical dulu
            ->orderBy('created_at', 'desc')
            ->limit(5) // Maksimal 5 insights dalam carousel
            ->get();
        
        return view('dashboard', compact('unreadInsights'));
    }
}