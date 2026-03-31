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
        $query = PaymentTransaction::with(['user', 'tier', 'plan']);

        // Status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('external_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest()
            ->paginate(20)
            ->withQueryString();

        // Stats
        $stats = [
            'total_revenue' => PaymentTransaction::where('status', 'success')->sum('amount'),
            'successful_count' => PaymentTransaction::where('status', 'success')->count(),
            'pending_count' => PaymentTransaction::where('status', 'pending')->count(),
            'monthly_revenue' => PaymentTransaction::where('status', 'success')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
        ];

        return view('admin.subscription.payments.index', compact('payments', 'status', 'stats'));
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
