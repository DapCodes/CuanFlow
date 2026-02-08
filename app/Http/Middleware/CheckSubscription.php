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

        if (! $user) {
            return redirect()->route('login');
        }

        // Admins bypass subscription check
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Exclude subscription, payment, and profile routes (so they can logout/pay/register outlet)
        if ($request->routeIs('subscription.*', 'payment.*', 'profile.*', 'outlets.register.*', 'logout')) {
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
                    'Langganan Anda sudah berakhir. Anda memiliki '.$graceDays.' hari masa tenggang.'
                );

                return $next($request);

            case FeatureAccessService::STATUS_PENDING:
                return $this->handlePendingVerification($request, $next);

            case FeatureAccessService::STATUS_EXPIRED:
                // For expired, check if past grace period
                return $this->handleExpiredSubscription($request, $next, $user);

            case FeatureAccessService::STATUS_NO_SUBSCRIPTION:
                // New user - don't show subscription modal immediately
                // Let the dashboard handle the onboarding flow
                return $this->handleNewUser($request, $next);

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

        if (! $subscription) {
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

        // Check for rejected trial request if status is cancelled or no_subscription
        if (in_array($reason, [\App\Services\FeatureAccessService::STATUS_CANCELLED, \App\Services\FeatureAccessService::STATUS_NO_SUBSCRIPTION])) {
            $rejectedTrial = \App\Models\TrialVerificationRequest::where('user_id', $request->user()->id)
                ->where('status', 'rejected')
                ->latest()
                ->first();

            if ($rejectedTrial) {
                // Check if 7 days have passed since rejection
                $canRetry = false;
                $waitTime = '';
                
                if ($rejectedTrial->updated_at) {
                    $rejectionDate = $rejectedTrial->updated_at;
                    $retryDate = $rejectionDate->copy()->addDays(7);
                    
                    if (now()->greaterThanOrEqualTo($retryDate)) {
                        $canRetry = true;
                    } else {
                        $diff = now()->diff($retryDate);
                        $parts = [];
                        if ($diff->d > 0) $parts[] = $diff->d . ' Hari';
                        if ($diff->h > 0) $parts[] = $diff->h . ' Jam';
                        if (empty($parts) && $diff->i > 0) $parts[] = $diff->i . ' Menit';
                        
                        $waitTime = implode(' ', $parts) ?: 'Beberapa saat';
                    }
                }

                session([
                    'show_subscription_modal' => true, 
                    'subscription_modal_reason' => 'trial_rejected',
                    'subscription_rejection_notes' => $rejectedTrial->admin_notes,
                    'subscription_retry_available' => $canRetry,
                    'subscription_retry_wait_time' => $waitTime,
                ]);
                
                if ($request->routeIs('dashboard')) {
                    return $next($request);
                }

                return redirect()->route('dashboard');
            }
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

        // Set session to show modal
        session([
            'show_subscription_modal' => true,
            'subscription_modal_reason' => 'pending_verification',
        ]);

        if ($request->routeIs('dashboard')) {
            return $next($request);
        }

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

    /**
     * Handle expired subscription - check if past grace period.
     */
    private function handleExpiredSubscription(Request $request, Closure $next, $user): Response
    {
        // Get the latest subscription to check grace period
        $subscription = $user->subscriptions()
            ->whereIn('status', [
                \App\Models\UserSubscription::STATUS_EXPIRED,
                \App\Models\UserSubscription::STATUS_ACTIVE,
                \App\Models\UserSubscription::STATUS_TRIAL,
            ])
            ->latest()
            ->first();

        if ($subscription) {
            $graceDaysRemaining = $subscription->grace_days_remaining;

            // If still within grace period, allow access with warning
            if ($graceDaysRemaining > 0) {
                session()->flash('subscription_warning',
                    'Langganan Anda sudah berakhir. Anda memiliki '.$graceDaysRemaining.' hari masa tenggang.'
                );

                return $next($request);
            }
        }

        // Past grace period - show subscription modal
        return $this->handleSubscriptionIssue($request, $next, FeatureAccessService::STATUS_EXPIRED);
    }

    /**
     * Handle new user without subscription - allow onboarding flow.
     */
    private function handleNewUser(Request $request, Closure $next): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'subscription_required',
                'message' => 'Anda belum memiliki langganan aktif.',
                'reason' => 'no_subscription',
            ], 402);
        }

        $user = $request->user();

        // For dashboard, set appropriate flags based on user state
        if ($request->routeIs('dashboard')) {
            // Set a flag to indicate this is a new user needing onboarding
            session(['new_user_onboarding' => true]);

            // If user has outlet but no subscription completed tour, show subscription choice modal
            // But ONLY if we are not already showing the main subscription modal (user clicked 'Buy')
            if ($user->outlet_id && ! session('show_welcome_tour') && ! session('show_subscription_modal')) {
                // Check if they haven't completed onboarding yet
                session(['force_subscription_choice' => true]);
            }

            return $next($request);
        }

        // For other routes, redirect to dashboard to start onboarding
        session(['new_user_onboarding' => true]);

        return redirect()->route('dashboard');
    }
}
