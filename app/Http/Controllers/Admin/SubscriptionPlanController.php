<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTier;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $query = SubscriptionPlan::with('tier')
            ->orderBy('tier_id')
            ->orderBy('duration_months');

        // Search
        if (request('search')) {
            $search = request('search');
            $query->whereHas('tier', function($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $plans = $query->get();

        // Stats
        $stats = [
            'total_plans' => SubscriptionPlan::count(),
            'active_plans' => SubscriptionPlan::where('is_active', true)->count(),
            'max_discount' => (int)SubscriptionPlan::max('discount_percentage'),
            'avg_price' => round(SubscriptionPlan::avg('price') ?? 0, 0),
        ];

        return view('admin.subscription.plans.index', compact('plans', 'stats'));
    }

    public function create()
    {
        $tiers = SubscriptionTier::orderBy('sort_order')->get();

        return view('admin.subscription.plans.create', compact('tiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tier_id' => ['required', 'exists:subscription_tiers,id'],
            'duration_months' => ['nullable', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
            'is_unlimited' => ['boolean'],
        ]);

        SubscriptionPlan::create([
            'tier_id' => $validated['tier_id'],
            'duration_months' => $request->boolean('is_unlimited') ? null : $validated['duration_months'],
            'price' => $validated['price'],
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'is_unlimited' => $request->boolean('is_unlimited'),
        ]);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Opsi durasi langganan berhasil dibuat.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        $tiers = SubscriptionTier::orderBy('sort_order')->get();

        return view('admin.subscription.plans.edit', [
            'subscriptionPlan' => $plan,
            'tiers' => $tiers,
        ]);
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'tier_id' => ['required', 'exists:subscription_tiers,id'],
            'duration_months' => ['nullable', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
            'is_unlimited' => ['boolean'],
        ]);

        $plan->update([
            'tier_id' => $validated['tier_id'],
            'duration_months' => $request->boolean('is_unlimited') ? null : $validated['duration_months'],
            'price' => $validated['price'],
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'is_unlimited' => $request->boolean('is_unlimited'),
        ]);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Opsi durasi langganan berhasil diperbarui.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus opsi ini karena sedang digunakan oleh pelanggan.');
        }

        $plan->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Opsi durasi langganan berhasil dihapus.');
    }
}
