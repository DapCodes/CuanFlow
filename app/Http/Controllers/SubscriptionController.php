<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTier;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display the pricing/subscription page.
     * This can also be loaded via AJAX for the modal.
     */
    public function index(Request $request)
    {
        $tiers = SubscriptionTier::active()
            ->with(['plans' => function ($q) {
                $q->active()->orderBy('duration_months');
            }])
            ->orderBy('sort_order')
            ->get();

        // If request is AJAX, return partial view
        if ($request->ajax()) {
            return view('subscription.partials.pricing_cards', compact('tiers'));
        }

        return view('subscription.index', compact('tiers'));
    }

    /**
     * Handle plan selection.
     * Redirects to payment or starts trial.
     */
    public function selectPlan(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        $plan = SubscriptionPlan::with('tier')->findOrFail($validated['plan_id']);

        // Check if user is eligible (e.g., trying to downgrade?)
        // For now, allow any selection

        // Redirect to payment page (Midtrans)
        return redirect()->route('subscription.payment', ['plan' => $plan->id]);
    }

    /**
     * Request free trial (without payment).
     * This is for the "Try Free" flow which requires approval.
     */
    public function requestTrial(Request $request)
    {
        // Check if user already had a trial
        if (auth()->user()->subscriptions()->where('is_trial', true)->exists()) {
            return back()->with('error', 'Anda sudah pernah menggunakan masa percobaan gratis.');
        }

        // Redirect to verification form
        return redirect()->route('subscription.trial-verification');
    }

    public function storeTrialVerification(Request $request)
    {
        $validated = $request->validate([
            'outlet_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'max:100'],
            'business_description' => ['nullable', 'string'],
            'photo_store_front' => ['required', 'image', 'max:5120'], // 5MB
            'photo_products' => ['required', 'image', 'max:5120'],
        ]);

        $paths = [];
        if ($request->hasFile('photo_store_front')) {
            $paths['store_front'] = $request->file('photo_store_front')->store('trial-verifications', 'public');
        }
        if ($request->hasFile('photo_products')) {
            $paths['products'] = $request->file('photo_products')->store('trial-verifications', 'public');
        }

        // Create verification request
        auth()->user()->trialRequests()->create([
            'outlet_name' => $validated['outlet_name'],
            'business_type' => $validated['business_type'],
            'business_description' => $validated['business_description'],
            'photo_store_front_path' => $paths['store_front'] ?? null,
            'photo_products_path' => $paths['products'] ?? null,
            'status' => 'pending',
        ]);

        // Create pending subscription to block immediate access but allow later activation
        // We use the ID of the 'silver' tier or the first tier
        $tier = SubscriptionTier::where('name', 'silver')->first() ?? SubscriptionTier::first();

        auth()->user()->subscriptions()->create([
            'tier_id' => $tier->id,
            'status' => \App\Models\UserSubscription::STATUS_PENDING_VERIFICATION,
            'started_at' => now(),
            'is_trial' => true, // Intent is trial
        ]);
        // Mark onboarding as completed and set session to show pending status
        session([
            'show_subscription_modal' => true,
            'subscription_modal_reason' => 'pending_verification',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Permintaan verifikasi trial berhasil dikirim. Tim kami akan meninjau data Anda dalam 1x24 jam.');
    }
}
