<?php

namespace App\Http\Middleware;

use App\Models\Feature;
use App\Services\FeatureAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureAccess
{
    protected FeatureAccessService $accessService;

    public function __construct(FeatureAccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    /**
     * Handle an incoming request.
     * Checks BOTH subscription validity AND tier feature access.
     *
     * @param  string|null  $featureName  The feature name to check access for
     */
    public function handle(Request $request, Closure $next, ?string $featureName = null): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // If no specific feature is specified, just proceed
        if (! $featureName) {
            return $next($request);
        }

        // Check if feature exists and is active globally
        $feature = Feature::where('name', $featureName)->first();

        if (! $feature) {
            abort(404, 'Fitur tidak ditemukan.');
        }

        if (! $feature->is_active) {
            return redirect()->route('dashboard')
                ->with('error', 'Fitur tersebut tidak dapat di buka dalam tahap perbaikan.');
        }

        // Admins bypass subscription/billing checks
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Use the centralized service for access check
        $accessResult = $this->accessService->checkAccess($user, $featureName);

        if (! $accessResult['can_access']) {
            return $this->handleNoAccess($request, $feature, $accessResult);
        }

        // If in grace period, flash a warning message
        if ($accessResult['status'] === FeatureAccessService::STATUS_GRACE) {
            session()->flash('subscription_warning',
                'Langganan Anda sudah berakhir. Anda memiliki '.$accessResult['grace_days'].' hari masa tenggang.'
            );
        }

        return $next($request);
    }

    /**
     * Handle when user doesn't have access to the feature.
     */
    private function handleNoAccess(Request $request, Feature $feature, array $accessResult): Response
    {
        $status = $accessResult['status'];

        // If subscription issue (not feature-tier issue)
        if (in_array($status, [
            FeatureAccessService::STATUS_EXPIRED,
            FeatureAccessService::STATUS_NO_SUBSCRIPTION,
            FeatureAccessService::STATUS_CANCELLED,
        ])) {
            return $this->handleSubscriptionIssue($request, $status, $accessResult['reason']);
        }

        // Feature-tier issue (user has subscription but tier doesn't include this feature)
        return $this->handleFeatureLocked($request, $feature);
    }

    /**
     * Handle subscription-related access denial.
     */
    private function handleSubscriptionIssue(Request $request, string $status, string $reason): Response
    {
        $user = $request->user();

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'subscription_required',
                'message' => $reason,
                'status' => $status,
                'upgrade_url' => $user->hasRole('owner') ? route('subscription.index') : null,
            ], 402);
        }

        // Only owners can see and interact with subscription modals
        if ($user->hasRole('owner')) {
            // Redirect to dashboard with subscription modal
            session([
                'show_subscription_modal' => true,
                'subscription_modal_reason' => $status,
            ]);

            return redirect()->route('dashboard');
        }

        // Non-owners (employees) get redirected to locked page
        session(['employee_lock_reason' => 'no_subscription']);

        return redirect()->route('employee.locked');
    }

    /**
     * Handle feature locked (tier doesn't include feature).
     */
    private function handleFeatureLocked(Request $request, Feature $feature): Response
    {
        // Find the minimum tier required for this feature
        $requiredTier = $feature->tiers()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        $requiredTierName = $requiredTier?->display_name ?? 'Paket yang lebih tinggi';

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'feature_locked',
                'message' => "Fitur '{$feature->display_name}' memerlukan paket {$requiredTierName}.",
                'feature' => [
                    'name' => $feature->name,
                    'display_name' => $feature->display_name,
                    'required_tier' => $requiredTierName,
                ],
                'upgrade_url' => route('subscription.index'),
            ], 403);
        }

        session([
            'show_upgrade_modal' => true,
            'locked_feature' => [
                'name' => $feature->name,
                'display_name' => $feature->display_name,
                'required_tier' => $requiredTierName,
            ],
        ]);

        return redirect()->route('dashboard')->with('error', "Fitur '{$feature->display_name}' memerlukan paket {$requiredTierName}. Upgrade untuk mengakses.");
    }
}
