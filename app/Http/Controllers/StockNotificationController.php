<?php

namespace App\Http\Controllers;

use App\Models\StockNotification;
use Illuminate\Http\Request;

class StockNotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index(Request $request)
    {
        $outletId = auth()->user()->outlet_id;
        $userId = auth()->id();
        $type = $request->query('type', 'all');

        // Check stock first to ensure data is fresh
        app(\App\Services\StockNotificationService::class)->checkAllStock($outletId);

        $query = StockNotification::where('outlet_id', $outletId)
            ->where('is_read', false);

        // Apply type filter
        if ($type === 'product') {
            $query->where('stockable_type', \App\Models\Product::class);
        } elseif ($type === 'stock') {
            $query->where('stockable_type', \App\Models\RawMaterial::class);
        }

        // Join with reads to handle 'unread' filter and sorting
        $query->leftJoin('stock_notification_reads', function($join) use ($userId) {
            $join->on('stock_notifications.id', '=', 'stock_notification_reads.stock_notification_id')
                 ->where('stock_notification_reads.user_id', '=', $userId);
        });

        if ($type === 'unread') {
            $query->whereNull('stock_notification_reads.read_at');
        }

        $notifications = $query->select('stock_notifications.*', 'stock_notification_reads.read_at as my_read_at')
            ->orderByRaw('CASE WHEN stock_notification_reads.read_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('stock_notifications.created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        foreach ($notifications as $notification) {
            $notification->is_read_by_me = !is_null($notification->my_read_at);
        }

        return view('stock-notifications.index', compact('notifications', 'type'));
    }

    /**
     * Mark a notification as read for the current user.
     */
    public function markAsRead($id)
    {
        $notification = StockNotification::findOrFail($id);
        $userId = auth()->id();

        // Sync without detaching to ensure we don't duplicate on multiple clicks
        $notification->readByUsers()->syncWithoutDetaching([$userId => ['read_at' => now()]]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for the current user in this outlet.
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        $outletId = $user->outlet_id;

        $unreadNotifications = StockNotification::where('outlet_id', $outletId)
            ->where('is_read', false)
            ->unreadBy($user->id)
            ->get();

        foreach ($unreadNotifications as $notification) {
            $notification->readByUsers()->syncWithoutDetaching([$user->id => ['read_at' => now()]]);
        }

        return response()->json(['success' => true]);
    }
}
