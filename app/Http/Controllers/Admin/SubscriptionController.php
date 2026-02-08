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

        $subscriptions = UserSubscription::with(['user', 'tier', 'plan'])
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.subscription.index', compact('subscriptions', 'status'));
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
