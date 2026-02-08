<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'tier_id',
        'plan_id',
        'status',
        'started_at',
        'expires_at',
        'is_trial',
        'trial_ends_at',
        'auto_renew',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'tier_id' => 'integer',
        'plan_id' => 'integer',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_trial' => 'boolean',
        'trial_ends_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    public const STATUS_TRIAL = 'trial';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_PENDING_VERIFICATION = 'pending_verification';

    /**
     * The user who owns this subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The subscription tier.
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(SubscriptionTier::class, 'tier_id');
    }

    /**
     * The subscription plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Payment transactions for this subscription.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'subscription_id');
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_TRIAL]);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        if ($this->status === self::STATUS_ACTIVE) {
            return is_null($this->expires_at) || $this->expires_at->isFuture();
        }

        if ($this->status === self::STATUS_TRIAL) {
            return is_null($this->trial_ends_at) || $this->trial_ends_at->isFuture();
        }

        return false;
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    /**
     * Check if subscription is trial.
     */
    public function isTrial(): bool
    {
        return $this->is_trial && $this->status === self::STATUS_TRIAL;
    }

    /**
     * Check if subscription is pending verification.
     */
    public function isPendingVerification(): bool
    {
        return $this->status === self::STATUS_PENDING_VERIFICATION;
    }

    /**
     * Get days remaining until expiration.
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if ($this->status === self::STATUS_EXPIRED) {
            return 0;
        }

        $expiryDate = $this->is_trial ? $this->trial_ends_at : $this->expires_at;

        if (is_null($expiryDate)) {
            return null; // Unlimited
        }

        return max(0, (int) Carbon::now()->diffInDays($expiryDate, false));
    }

    /**
     * Get days remaining in grace period.
     */
    public function getGraceDaysRemainingAttribute(): int
    {
        // Grace period applies if status is EXPIRED or (ACTIVE/TRIAL with passed date)
        if ($this->status === self::STATUS_CANCELLED || $this->status === self::STATUS_PENDING_VERIFICATION) {
            return 0;
        }

        // If technically active (valid date), no grace period needed
        if ($this->isActive()) {
            return 0;
        }

        $graceDays = SubscriptionSetting::getGracePeriodDays();
        $expiryDate = $this->is_trial ? $this->trial_ends_at : $this->expires_at;

        if (is_null($expiryDate)) {
            return 0;
        }

        $daysSinceExpiry = (int) Carbon::now()->diffInDays($expiryDate, false);

        return max(0, $graceDays + $daysSinceExpiry);
    }

    /**
     * Check if subscription is within grace period.
     */
    public function isInGracePeriod(): bool
    {
        if ($this->grace_days_remaining > 0) {
            return true;
        }

        return false;
    }

    /**
     * Activate the subscription.
     */
    public function activate(SubscriptionPlan $plan, ?Carbon $startDate = null): self
    {
        $startDate = $startDate ?? Carbon::now();

        $this->update([
            'plan_id' => $plan->id,
            'status' => self::STATUS_ACTIVE,
            'started_at' => $startDate,
            'expires_at' => $plan->calculateExpiryDate($startDate),
            'is_trial' => false,
            'trial_ends_at' => null,
        ]);

        return $this;
    }

    /**
     * Start trial.
     */
    public function startTrial(?int $days = null): self
    {
        $days = $days ?? SubscriptionSetting::getTrialDays();

        $this->update([
            'status' => self::STATUS_TRIAL,
            'started_at' => Carbon::now(),
            'is_trial' => true,
            'trial_ends_at' => Carbon::now()->addDays($days),
        ]);

        return $this;
    }

    /**
     * Mark as expired.
     */
    public function markExpired(): self
    {
        $this->update(['status' => self::STATUS_EXPIRED]);

        return $this;
    }

    /**
     * Cancel subscription.
     */
    public function cancel(): self
    {
        $this->update(['status' => self::STATUS_CANCELLED, 'auto_renew' => false]);

        return $this;
    }
}
