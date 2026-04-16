<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
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
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($qu) use ($search) {
                    $qu->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('tier', function ($qt) use ($search) {
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

        $allPlans = SubscriptionPlan::with('tier')->active()->get();

        return view('admin.subscription.index', compact('subscriptions', 'status', 'stats', 'allPlans'));
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

    public function searchUsers(Request $request)
    {
        $search = $request->query('query');

        if (! $search) {
            return response()->json([]);
        }

        $users = User::where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        $plan = SubscriptionPlan::with('tier')->findOrFail($validated['plan_id']);

        $startedAt = Carbon::now();
        $expiresAt = $plan->calculateExpiryDate($startedAt);

        // Cancel existing active subscriptions for this user to avoid conflicts
        UserSubscription::where('user_id', $validated['user_id'])
            ->whereIn('status', [UserSubscription::STATUS_ACTIVE, UserSubscription::STATUS_TRIAL])
            ->update(['status' => UserSubscription::STATUS_CANCELLED]);

        UserSubscription::create([
            'user_id' => $validated['user_id'],
            'tier_id' => $plan->tier_id,
            'plan_id' => $plan->id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'started_at' => $startedAt,
            'expires_at' => $expiresAt,
            'is_trial' => false,
            'auto_renew' => false,
        ]);

        return back()->with('success', 'Pelanggan berhasil ditambahkan.');
    }
}
