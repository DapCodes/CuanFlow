<?php

namespace App\Http\Middleware;

use App\Models\Feature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureAccess
{
    /**
     * Handle an incoming request.
     * Checks if the user's subscription tier has access to the specified feature.
     *
     * @param string|null $featureName The feature name to check access for
     */
    public function handle(Request $request, Closure $next, ?string $featureName = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admins bypass feature access check
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // If no specific feature is specified, just proceed
        if (!$featureName) {
            return $next($request);
        }

        // Check if feature exists and is active
        $feature = Feature::where('name', $featureName)->where('is_active', true)->first();

        if (!$feature) {
            // Feature doesn't exist or is disabled globally
            abort(404, 'Fitur tidak ditemukan.');
        }

        // Check if user can access this feature
        if (!$user->canAccessFeature($featureName)) {
            return $this->handleNoAccess($request, $feature);
        }

        return $next($request);
    }

    /**
     * Handle when user doesn't have access to the feature.
     */
    private function handleNoAccess(Request $request, Feature $feature): Response
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
                'upgrade_url' => route('subscription.plans'),
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

        return redirect()->back()->with('error', "Fitur '{$feature->display_name}' memerlukan paket {$requiredTierName}. Upgrade untuk mengakses.");
    }
}
