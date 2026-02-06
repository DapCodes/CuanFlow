<?php

namespace App\Http\Middleware;

use App\Services\FeatureAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    protected FeatureAccessService $accessService;

    public function __construct(FeatureAccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    /**
     * Handle an incoming request.
     * Checks if the user has an active or grace-period subscription.
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

        // Get subscription status from centralized service
        $status = $this->accessService->getSubscriptionStatus($user);

        switch ($status) {
            case FeatureAccessService::STATUS_ACTIVE:
                // Clear any pending modal sessions
                if (session()->has('show_subscription_modal')) {
                    session()->forget(['show_subscription_modal', 'subscription_modal_reason']);
                }
                
                // Check for upcoming expiry warning (7 days)
                $this->checkExpiryWarning($user);
                return $next($request);

            case FeatureAccessService::STATUS_GRACE:
                // Allow access but show warning
                $graceDays = $this->accessService->getGraceDaysRemaining($user);
                session()->flash('subscription_warning', 
                    'Langganan Anda sudah berakhir. Anda memiliki ' . $graceDays . ' hari masa tenggang.'
                );
                return $next($request);

            case FeatureAccessService::STATUS_PENDING:
                return $this->handlePendingVerification($request, $next);

            case FeatureAccessService::STATUS_EXPIRED:
            case FeatureAccessService::STATUS_NO_SUBSCRIPTION:
            case FeatureAccessService::STATUS_CANCELLED:
            default:
                return $this->handleSubscriptionIssue($request, $next, $status);
        }
    }

    /**
     * Check for upcoming subscription expiry and flash warning.
     */
    private function checkExpiryWarning($user): void
    {
        $subscription = $user->subscription;
        
        if (!$subscription) {
            return;
        }

        $daysRemaining = $subscription->days_remaining;

        if ($daysRemaining !== null && $daysRemaining <= 7 && $daysRemaining > 0) {
            $type = $subscription->isTrial() ? 'trial' : 'langganan';
            session()->flash('subscription_warning', 
                "Masa {$type} Anda akan berakhir dalam {$daysRemaining} hari."
            );
        }
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

        // Otherwise redirect to dashboard with modal
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
            FeatureAccessService::STATUS_NO_SUBSCRIPTION => 'Anda belum memiliki langganan aktif. Pilih paket untuk melanjutkan.',
            FeatureAccessService::STATUS_EXPIRED => 'Langganan Anda telah berakhir. Perpanjang untuk melanjutkan.',
            FeatureAccessService::STATUS_CANCELLED => 'Langganan Anda telah dibatalkan. Berlangganan kembali untuk mengakses fitur.',
            default => 'Terjadi masalah dengan langganan Anda.',
        };
    }
}
