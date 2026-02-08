<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class SubscriptionPaymentController extends Controller
{
    /**
     * Show payment page / Midtrans Snap.
     */
    public function show(Request $request)
    {
        $planId = $request->query('plan');

        if (! $planId) {
            return redirect()->route('subscription.index');
        }

        $plan = SubscriptionPlan::with('tier')->findOrFail($planId);

        // Ensure plan is active
        if (! $plan->is_active) {
            return back()->with('error', 'Paket tidak tersedia.');
        }

        // Configure Midtrans
        Config::$serverKey = config('services.midtrans.server_key') ?? env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = config('services.midtrans.is_production', false) ?? env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Create Transaction Data
        $orderId = 'SUBS-'.auth()->id().'-'.time().'-'.Str::random(5);
        $user = auth()->user();

        // Calculate Amount (Add Logic for Taxes/Discounts if needed)
        // For now, simple price
        $amount = (int) $plan->price;

        // Add Tax (11% PPN)
        $tax = (int) ($amount * 0.11);
        $total = $amount + $tax;

        // Store Transaction
        \App\Models\PaymentTransaction::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'tier_id' => $plan->tier_id,
            'transaction_id' => $orderId,
            'amount' => $total,
            'status' => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone, // Ensure phone exists
            ],
            'item_details' => [
                [
                    'id' => 'PLAN-'.$plan->id,
                    'price' => $total, // Total per item
                    'quantity' => 1,
                    'name' => $plan->tier->display_name.' ('.$plan->duration_months.' Bulan)',
                ],
            ],
            'callbacks' => [
                'finish' => route('subscription.payment.finish'),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: '.$e->getMessage());
        }

        return view('subscription.payment', compact('plan', 'snapToken', 'orderId', 'amount', 'tax', 'total'));
    }

    /**
     * Handle payment success/callback (Frontend).
     */
    public function finish(Request $request)
    {
        // Midtrans redirect here after payment
        return redirect()->route('dashboard')->with('success', 'Pembayaran berhasil diproses. Langganan Anda aktif.');
    }

    /**
     * Handle payment error (Frontend).
     */
    public function error(Request $request)
    {
        return redirect()->route('subscription.index')->with('error', 'Pembayaran gagal atau dibatalkan.');
    }
}
