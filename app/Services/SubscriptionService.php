<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTier;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Activate a subscription for a user.
     */
    public function activateSubscription(User $user, SubscriptionPlan $plan): UserSubscription
    {
        return DB::transaction(function () use ($user, $plan) {
            // Cancel any existing active subscriptions
            $user->subscriptions()
                ->whereIn('status', [UserSubscription::STATUS_ACTIVE, UserSubscription::STATUS_TRIAL])
                ->update(['status' => UserSubscription::STATUS_CANCELLED]);

            // Create new subscription
            $startDate = Carbon::now();
            $expiryDate = $plan->calculateExpiryDate($startDate);

            $subscription = $user->subscriptions()->create([
                'tier_id' => $plan->tier_id,
                'plan_id' => $plan->id,
                'status' => UserSubscription::STATUS_ACTIVE,
                'started_at' => $startDate,
                'expires_at' => $expiryDate,
                'is_trial' => false,
                'auto_renew' => true, // Can be made configurable
            ]);

            // Clear cache
            $user->clearSubscriptionCache();

            return $subscription;
        });
    }

    /**
     * Start a free trial for a user.
     */
    public function startTrial(User $user, int $days = 30): UserSubscription
    {
        return DB::transaction(function () use ($user, $days) {
            // Cancel any existing subscriptions
            $user->subscriptions()
                ->whereIn('status', [UserSubscription::STATUS_ACTIVE, UserSubscription::STATUS_TRIAL])
                ->update(['status' => UserSubscription::STATUS_CANCELLED]);

            // Get silver tier (default for trial) or lowest paid tier
            $tier = SubscriptionTier::where('name', 'silver')->first() 
                 ?? SubscriptionTier::orderBy('price', 'asc')->first();

            $subscription = $user->subscriptions()->create([
                'tier_id' => $tier->id,
                'status' => UserSubscription::STATUS_TRIAL,
                'started_at' => Carbon::now(),
                'trial_ends_at' => Carbon::now()->addDays($days),
                'is_trial' => true,
            ]);

            // Clear cache
            $user->clearSubscriptionCache();

            return $subscription;
        });
    }

    /**
     * Cancel current subscription.
     */
    public function cancelSubscription(User $user): bool
    {
        $subscription = $user->subscription;

        if (!$subscription) {
            return false;
        }

        $subscription->cancel();
        $user->clearSubscriptionCache();

        return true;
    }
}
