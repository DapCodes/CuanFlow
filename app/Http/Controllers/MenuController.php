<?php

namespace App\Http\Controllers;

use App\Models\AiInsight;
use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\FeatureCategory;
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
            ? CashRegister::where('outlet_id', $user->outlet_id)
                ->where('user_id', $user->id)
                ->where('status', 'open')
                ->exists()
            : false;

        $unreadInsights = $user->outlet_id
            ? AiInsight::where('outlet_id', $user->outlet_id)
                ->unread()
                ->active()
                ->orderBy('severity', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
            : collect();

        $isReseller = $user->email
            ? Customer::where('email', $user->email)->where('type', 'reseller')->exists()
            : false;

        // Load feature categories with eager-loaded active items (avoid N+1)
        $categories = FeatureCategory::with(['featureItems' => function ($query) {
            $query->active();
        }])->active()->get();

        return view('dashboard', compact('unreadInsights', 'isPosOpen', 'isReseller', 'categories'));
    }
}
