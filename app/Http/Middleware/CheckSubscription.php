<?php

namespace App\Http\Middleware;

use App\Models\UserSubscription;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     * Checks if the user has an active subscription.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admins bypass subscription check
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Exclude subscription, payment, and profile routes (so they can logout/pay)
        if ($request->routeIs('subscription.*', 'payment.*', 'profile.*', 'logout')) {
            return $next($request);
        }

        $subscription = $user->subscription;

        // No subscription at all
        if (!$subscription) {
            return $this->handleSubscriptionIssue($request, $next, 'no_subscription');
        }

        // Check subscription status
        if ($subscription->status === UserSubscription::STATUS_PENDING_VERIFICATION) {
            return $this->handlePendingVerification($request, $next);
        }

        if ($subscription->status === UserSubscription::STATUS_CANCELLED) {
            return $this->handleSubscriptionIssue($request, $next, 'cancelled');
        }

        if ($subscription->status === UserSubscription::STATUS_EXPIRED) {
            // Check grace period
            if ($subscription->isInGracePeriod()) {
                session()->flash('subscription_warning', 'Langganan Anda sudah berakhir. Anda memiliki ' . $subscription->days_remaining . ' hari masa tenggang.');
                return $next($request);
            }
            return $this->handleSubscriptionIssue($request, $next, 'expired');
        }

        // Check if subscription is still valid
        if (!$subscription->isActive()) {
            return $this->handleSubscriptionIssue($request, $next, 'expired');
        }

        // Clear subscription modal session if subscription is active
        if (session()->has('show_subscription_modal')) {
            session()->forget(['show_subscription_modal', 'subscription_modal_reason']);
        }

        // Check trial expiry warning (7 days notice)
        if ($subscription->isTrial() && $subscription->days_remaining <= 7 && $subscription->days_remaining > 0) {
            session()->flash('subscription_warning', 'Masa trial Anda akan berakhir dalam ' . $subscription->days_remaining . ' hari.');
        }

        // Check subscription expiry warning (7 days notice)
        if (!$subscription->isTrial() && $subscription->days_remaining !== null && $subscription->days_remaining <= 7 && $subscription->days_remaining > 0) {
            session()->flash('subscription_warning', 'Langganan Anda akan berakhir dalam ' . $subscription->days_remaining . ' hari.');
        }

        return $next($request);
    }

    /**
     * Handle subscription issues (no sub, expired, etc).
     */
    private function handleSubscriptionIssue(Request $request, Closure $next, string $reason): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'subscription_required',
                'message' => $this->getReasonMessage($reason),
                'reason' => $reason,
            ], 402);
        }

        // If on dashboard, allow access but show modal
        if ($request->routeIs('dashboard')) {
            session(['show_subscription_modal' => true, 'subscription_modal_reason' => $reason]);
            return $next($request);
        }

        // Otherwise redirect to dashboard
        session(['show_subscription_modal' => true, 'subscription_modal_reason' => $reason]);
        return redirect()->route('dashboard');
    }

    /**
     * Handle pending verification status.
     */
    private function handlePendingVerification(Request $request, Closure $next): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'pending_verification',
                'message' => 'Permintaan trial Anda sedang ditinjau. Kami akan menghubungi Anda dalam 24-48 jam.',
            ], 202);
        }

        if ($request->routeIs('dashboard')) {
             session()->flash('info', 'Permintaan trial Anda sedang ditinjau. Kami akan menghubungi Anda dalam 24-48 jam.');
             return $next($request);
        }

        session()->flash('info', 'Permintaan trial Anda sedang ditinjau. Kami akan menghubungi Anda dalam 24-48 jam.');
        return redirect()->route('dashboard');
    }

    /**
     * Get user-friendly reason message.
     */
    private function getReasonMessage(string $reason): string
    {
        return match ($reason) {
            'no_subscription' => 'Anda belum memiliki langganan aktif. Pilih paket untuk melanjutkan.',
            'expired' => 'Langganan Anda telah berakhir. Perpanjang untuk melanjutkan.',
            'cancelled' => 'Langganan Anda telah dibatalkan. Berlangganan kembali untuk mengakses fitur.',
            default => 'Terjadi masalah dengan langganan Anda.',
        };
    }
}
