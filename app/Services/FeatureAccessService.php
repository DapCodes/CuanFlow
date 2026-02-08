<?php

namespace App\Services;

use App\Models\Feature;
use App\Models\SubscriptionSetting;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Cache;

class FeatureAccessService
{
    /**
     * Subscription status constants.
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_GRACE = 'grace';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_NO_SUBSCRIPTION = 'no_subscription';
    public const STATUS_PENDING = 'pending';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Check if user can access a specific feature.
     * This checks BOTH subscription validity AND tier feature access.
     */
    public function canAccess(User $user, string $featureName): bool
    {
        // Admins always have access
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check subscription status
        $status = $this->getSubscriptionStatus($user);
        
        // NEW USERS: Allow viewing all features during onboarding
        // They can see the features but the actual routes are protected by middleware
        if ($status === self::STATUS_NO_SUBSCRIPTION) {
            return true; // Show all features for onboarding tour
        }
        
        // Only allow if subscription is active or in grace period
        if (!in_array($status, [self::STATUS_ACTIVE, self::STATUS_GRACE])) {
            return false;
        }

        // Check if tier has the feature
        return $this->tierHasFeature($user, $featureName);
    }

    /**
     * Get the subscription status for a user.
     */
    public function getSubscriptionStatus(User $user): string
    {
        return Cache::remember("user_{$user->id}_sub_status", 60, function () use ($user) {
            return $this->computeSubscriptionStatus($user);
        });
    }

    /**
     * Compute the subscription status without caching.
     */
    protected function computeSubscriptionStatus(User $user): string
    {
        // Get the most recent subscription (not just active ones)
        $subscription = $user->subscriptions()
            ->whereIn('status', [
                UserSubscription::STATUS_ACTIVE,
                UserSubscription::STATUS_TRIAL,
                UserSubscription::STATUS_EXPIRED,
            ])
            ->latest()
            ->first();

        if (!$subscription) {
            return self::STATUS_NO_SUBSCRIPTION;
        }

        if ($subscription->status === UserSubscription::STATUS_CANCELLED) {
            return self::STATUS_CANCELLED;
        }

        if ($subscription->status === UserSubscription::STATUS_PENDING_VERIFICATION) {
            return self::STATUS_PENDING;
        }

        // Check if subscription is strictly active (date is in future)
        if ($subscription->isActive()) {
            return self::STATUS_ACTIVE;
        }

        // Check if within grace period
        if ($subscription->isInGracePeriod()) {
            return self::STATUS_GRACE;
        }

        // Otherwise expired
        return self::STATUS_EXPIRED;
    }

    /**
     * Check if user's tier has access to a feature.
     */
    public function tierHasFeature(User $user, string $featureName): bool
    {
        return Cache::remember("user_{$user->id}_feature_{$featureName}", 300, function () use ($user, $featureName) {
            // Get the user's tier from any subscription (active, trial, or even expired)
            $subscription = $user->subscriptions()
                ->whereIn('status', [
                    UserSubscription::STATUS_ACTIVE,
                    UserSubscription::STATUS_TRIAL,
                    UserSubscription::STATUS_EXPIRED,
                ])
                ->latest()
                ->first();

            $tier = $subscription?->tier;

            if (!$tier) {
                return false;
            }

            return $tier->hasFeature($featureName);
        });
    }

    /**
     * Get grace days remaining for user.
     */
    public function getGraceDaysRemaining(User $user): int
    {
        $subscription = $user->subscriptions()
            ->whereIn('status', [
                UserSubscription::STATUS_ACTIVE,
                UserSubscription::STATUS_TRIAL,
                UserSubscription::STATUS_EXPIRED,
            ])
            ->latest()
            ->first();

        return $subscription?->grace_days_remaining ?? 0;
    }

    /**
     * Get a detailed access result with status and reason.
     */
    public function checkAccess(User $user, string $featureName): array
    {
        $status = $this->getSubscriptionStatus($user);
        $hasFeature = $this->tierHasFeature($user, $featureName);
        $graceDays = $this->getGraceDaysRemaining($user);

        $canAccess = false;
        $reason = '';

        if ($user->hasRole('admin')) {
            return [
                'can_access' => true,
                'status' => 'admin',
                'reason' => 'Administrator bypass',
                'grace_days' => 0,
            ];
        }

        switch ($status) {
            case self::STATUS_ACTIVE:
                if ($hasFeature) {
                    $canAccess = true;
                    $reason = 'Subscription active and feature included.';
                } else {
                    $reason = 'Feature not included in your subscription tier.';
                }
                break;

            case self::STATUS_GRACE:
                if ($hasFeature) {
                    $canAccess = true;
                    $reason = "Grace period: {$graceDays} days remaining.";
                } else {
                    $reason = 'Feature not included in your subscription tier.';
                }
                break;

            case self::STATUS_EXPIRED:
                $reason = 'Subscription expired. Please renew to access features.';
                break;

            case self::STATUS_NO_SUBSCRIPTION:
                $reason = 'No subscription found. Please subscribe to access features.';
                break;

            case self::STATUS_PENDING:
                $reason = 'Subscription pending verification.';
                break;

            case self::STATUS_CANCELLED:
                $reason = 'Subscription cancelled. Please resubscribe to access features.';
                break;
        }

        return [
            'can_access' => $canAccess,
            'status' => $status,
            'reason' => $reason,
            'grace_days' => $graceDays,
            'has_feature' => $hasFeature,
        ];
    }

    /**
     * Clear cache for a user.
     */
    public function clearCache(User $user): void
    {
        Cache::forget("user_{$user->id}_sub_status");
        // Feature cache uses pattern, need to clear all
        $user->clearSubscriptionCache();
    }
}
