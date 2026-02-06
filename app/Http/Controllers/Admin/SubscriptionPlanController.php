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
        $plans = SubscriptionPlan::with('tier')
            ->orderBy('tier_id')
            ->orderBy('duration_months')
            ->get();

        return view('admin.subscription.plans.index', compact('plans'));
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
            'tiers' => $tiers
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
