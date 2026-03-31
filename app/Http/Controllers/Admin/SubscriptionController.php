<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of all user subscriptions.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = UserSubscription::with(['user', 'tier', 'plan']);

        // Status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    $qu->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('tier', function($qt) use ($search) {
                    $qt->where('display_name', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            });
        }

        $subscriptions = $query->latest()
            ->paginate(20)
            ->withQueryString();

        // Stats
        $stats = [
            'total' => UserSubscription::count(),
            'active' => UserSubscription::where('status', 'active')->count(),
            'trial' => UserSubscription::where('status', 'trial')->count(),
            'expired' => UserSubscription::where('status', 'expired')->count(),
        ];

        return view('admin.subscription.index', compact('subscriptions', 'status', 'stats'));
    }

    /**
     * Display details of a user subscription.
     */
    public function show(UserSubscription $subscription)
    {
        $subscription->load(['user', 'tier', 'plan', 'payments']);

        return view('admin.subscription.show', compact('subscription'));
    }

    /**
     * Manually update subscription status.
     */
    public function updateStatus(Request $request, UserSubscription $subscription)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,expired,cancelled,trial,pending_verification'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $subscription->update($validated);

        return back()->with('success', 'Status langganan berhasil diperbarui.');
    }
}
