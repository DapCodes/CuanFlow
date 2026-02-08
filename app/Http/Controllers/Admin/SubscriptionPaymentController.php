<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class SubscriptionPaymentController extends Controller
{
    /**
     * Display a listing of all payments.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $payments = PaymentTransaction::with(['user', 'tier', 'plan'])
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.subscription.payments.index', compact('payments', 'status'));
    }

    /**
     * Display details of a payment.
     */
    public function show(PaymentTransaction $payment)
    {
        $payment->load(['user', 'tier', 'plan', 'subscription']);

        return view('admin.subscription.payments.show', compact('payment'));
    }

    /**
     * Manually approve a pending payment (Admin override).
     */
    public function approve(PaymentTransaction $payment)
    {
        if (! $payment->isPending()) {
            return back()->with('error', 'Pembayaran ini tidak dalam status pending.');
        }

        $payment->markSuccessful(['admin_note' => 'Manually approved by admin'], 'Manual/Admin');

        // Logic to activate subscription if not already activated by webhooks
        if ($payment->subscription && $payment->subscription->isPendingVerification()) {
            $payment->subscription->activate($payment->plan);
        }

        return back()->with('success', 'Pembayaran berhasil disetujui secara manual.');
    }
}
