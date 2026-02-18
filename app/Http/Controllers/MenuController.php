<?php

namespace App\Http\Controllers;

use App\Models\AiInsight;
use App\Services\StockNotificationService;

class MenuController extends Controller
{
    protected $stockNotificationService;

    public function __construct(StockNotificationService $stockNotificationService)
    {
        $this->stockNotificationService = $stockNotificationService;
    }

    public function index()
    {
        $user = auth()->user();

        // Check stock and generate notifications ONLY if user has an outlet
        if ($user->outlet_id) {
            $this->stockNotificationService->checkAllStock($user->outlet_id);
        }

        // Cek apakah ada sesi POS yang sedang buka
        $isPosOpen = $user->outlet_id 
            ? \App\Models\CashRegister::where('outlet_id', $user->outlet_id)
                ->where('user_id', $user->id)
                ->where('status', 'open')
                ->exists()
            : false;

        // Ambil unread insights untuk user outlet ini
        $unreadInsights = $user->outlet_id
            ? AiInsight::where('outlet_id', $user->outlet_id)
                ->unread()
                ->active()
                ->orderBy('severity', 'desc') // Critical dulu
                ->orderBy('created_at', 'desc')
                ->limit(5) // Maksimal 5 insights dalam carousel
                ->get()
            : collect();

        return view('dashboard', compact('unreadInsights', 'isPosOpen'));
    }
}
