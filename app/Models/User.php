<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ColorPalette;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use CausesActivity, HasApiTokens, HasFactory, HasRoles, LogsActivity, MustVerifyEmailTrait, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'outlet_id', 'phone', 'avatar', 'color_palette_id', 'is_active', 'last_login_at',
        'google_id', 'google_avatar', 'email_verified_at', 'last_seen_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'outlet_id', 'is_active'])
            ->logOnlyDirty()
            ->useLogName('user')
            ->setDescriptionForEvent(fn (string $eventName) => "User {$this->name} was {$eventName}");
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function colorPalette(): BelongsTo
    {
        return $this->belongsTo(ColorPalette::class);
    }

    /**
     * Get the active color palette for this user (falls back to default).
     */
    public function getActivePalette(): ColorPalette
    {
        return $this->colorPalette ?? ColorPalette::getDefault();
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'cashier_id');
    }

    public function cashRegisters(): HasMany
    {
        return $this->hasMany(CashRegister::class);
    }

    public function aiChatSessions(): HasMany
    {
        return $this->hasMany(AiChatSession::class);
    }

    public function outletsOwned(): HasMany
    {
        return $this->hasMany(Outlet::class, 'owner_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function withdrawLock(): HasOne
    {
        return $this->hasOne(UserWithdrawLock::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeByOutlet($q, $outletId)
    {
        return $q->where('outlet_id', $outletId);
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function canAccessOutlet($outletId): bool
    {
        return $this->isOwner() || $this->outlet_id == $outletId;
    }

    public function getAvatarUrlAttribute(): string
    {
        // Priority: local avatar > google_avatar > default placeholder
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }

        if ($this->google_avatar) {
            return $this->google_avatar;
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=31694E&background=F0E491';
    }

    // =========================================================================
    // Subscription Relationships
    // =========================================================================

    /**
     * Get the user's active subscription.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)->whereIn('status', [
            UserSubscription::STATUS_ACTIVE,
            UserSubscription::STATUS_TRIAL,
        ])->latest();
    }

    /**
     * Get all user subscriptions.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the latest trial verification request.
     */
    public function trialRequest(): HasOne
    {
        return $this->hasOne(TrialVerificationRequest::class)->latest();
    }

    /**
     * Get all trial verification requests.
     */
    public function trialRequests(): HasMany
    {
        return $this->hasMany(TrialVerificationRequest::class);
    }

    /**
     * Get payment transactions.
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // =========================================================================
    // Subscription Helper Methods
    // =========================================================================

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return Cache::remember("user_{$this->id}_has_subscription", 300, function () {
            $subscription = $this->subscription;

            return $subscription && $subscription->isActive();
        });
    }

    /**
     * Get the user's current subscription tier.
     */
    public function getSubscriptionTier(): ?SubscriptionTier
    {
        $subscription = $this->subscription;

        return $subscription?->tier;
    }

    /**
     * Get the user's tier name.
     */
    public function getTierName(): ?string
    {
        return $this->getSubscriptionTier()?->name;
    }

    /**
     * Check if user can access a specific feature.
     * Uses centralized FeatureAccessService for consistent logic.
     */
    public function canAccessFeature(string $featureName): bool
    {
        // Admins always have access
        if ($this->hasRole('admin')) {
            return true;
        }

        $accessService = app(\App\Services\FeatureAccessService::class);

        return $accessService->canAccess($this, $featureName);
    }

    /**
     * Check if user has pending trial verification.
     */
    public function hasPendingTrialVerification(): bool
    {
        $trialRequest = $this->trialRequest;

        return $trialRequest && $trialRequest->isPending();
    }

    /**
     * Check if user is currently on trial.
     */
    public function isOnTrial(): bool
    {
        $subscription = $this->subscription;

        return $subscription && $subscription->status === UserSubscription::STATUS_TRIAL;
    }

    /**
     * Get the maximum outlets allowed for user's tier.
     */
    public function getMaxOutlets(): ?int
    {
        return $this->getSubscriptionTier()?->max_outlets;
    }

    /**
     * Check if user can create more outlets.
     */
    public function canCreateOutlet(): bool
    {
        $maxOutlets = $this->getMaxOutlets();

        // Unlimited outlets
        if (is_null($maxOutlets)) {
            return true;
        }

        $currentOutlets = $this->outletsOwned()->count();

        return $currentOutlets < $maxOutlets;
    }

    /**
     * Clear subscription cache for this user.
     */
    public function clearSubscriptionCache(): void
    {
        Cache::forget("user_{$this->id}_has_subscription");
        Cache::flush(); // Also clear feature cache
    }
}
