<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class SubscriptionManagementController extends Controller
{
    /**
     * Display the subscription management dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        // Ensure user has a subscription (even if expired)
        if (! $subscription) {
            return redirect()->route('subscription.index');
        }

        $currentTier = $subscription->tier;

        // Plans for "Add Duration" (Extension) - Same Tier
        $extensionPlans = $currentTier->plans()
            ->where('is_active', true)
            ->orderBy('duration_months')
            ->get();

        // Tiers for "Upgrade" - Higher Priced Tiers
        // Logic: active tiers that are 'more expensive' or higher sort order than current
        $upgradeTiers = SubscriptionTier::where('is_active', true)
            ->where('id', '!=', $currentTier->id)
            ->where('price', '>', $currentTier->price) // Assuming price determines hierarchy
            ->with(['plans' => function ($q) {
                $q->where('is_active', true)->orderBy('duration_months');
            }, 'features']) // Eager load features
            ->orderBy('price')
            ->get();

        return view('subscription.manage', compact('subscription', 'currentTier', 'extensionPlans', 'upgradeTiers'));
    }

    /**
     * Process "Add Duration" (Extension) request.
     */
    public function addDuration(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = auth()->user();
        $subscription = $user->subscription;

        if (! $subscription) {
            return response()->json(['success' => false, 'message' => 'Subscription not found.'], 404);
        }

        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Verify plan belongs to current tier
        if ($plan->tier_id !== $subscription->tier_id) {
            return response()->json(['success' => false, 'message' => 'Invalid plan for extension.'], 400);
        }

        // Create Payment Transaction
        $snapToken = $this->createSnapToken($user, $plan, 'SUBS-EXT-');

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
        ]);
    }

    /**
     * Process "Upgrade Tier" request.
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = auth()->user();
        $subscription = $user->subscription;

        if (! $subscription) {
            return response()->json(['success' => false, 'message' => 'Subscription not found.'], 404);
        }

        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Verify plan does NOT belong to current tier (it is an upgrade)
        if ($plan->tier_id === $subscription->tier_id) {
            return response()->json(['success' => false, 'message' => 'Please use extension for the same tier.'], 400);
        }

        // Create Payment Transaction
        $snapToken = $this->createSnapToken($user, $plan, 'SUBS-UPG-');

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
        ]);
    }

    /**
     * Helper to create Midtrans Snap Token
     */
    private function createSnapToken($user, $plan, $prefix)
    {
        // Configure Midtrans
        Config::$serverKey = config('services.midtrans.server_key') ?? env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = config('services.midtrans.is_production', false) ?? env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = $prefix.$user->id.'-'.time().'-'.Str::random(5);

        $amount = (int) $plan->price;
        $tax = (int) ($amount * 0.11);
        $total = $amount + $tax;

        // Store Transaction
        PaymentTransaction::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'tier_id' => $plan->tier_id,
            'subscription_id' => $user->subscription->id, // Link to current subscription
            'transaction_id' => $orderId,
            'amount' => $total,
            'status' => 'pending',
            'payment_method' => 'qris', // Default/Initial
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'PLAN-'.$plan->id,
                    'price' => $total,
                    'quantity' => 1,
                    'name' => ($prefix === 'SUBS-EXT-' ? 'Perpanjangan ' : 'Upgrade ').$plan->tier->display_name,
                ],
            ],
            // Only enabled Qris / E-Wallet as per requirement usually, but let's keep it open or strictly qris if desired
            // 'enabled_payments' => ['gopay', 'shopeepay', 'other_qris'],
            'callbacks' => [
                'finish' => route('subscription.payment.finish'),
            ],
        ];

        return Snap::getSnapToken($params);
    }
}
