<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SubscriptionSetting extends Model
{
    protected $fillable = [
        'trial_duration_days',
        'grace_period_days',
        'enable_trial',
        'require_trial_verification',
        'auto_renew_default',
    ];

    protected $casts = [
        'trial_duration_days' => 'integer',
        'grace_period_days' => 'integer',
        'enable_trial' => 'boolean',
        'require_trial_verification' => 'boolean',
        'auto_renew_default' => 'boolean',
    ];

    /**
     * Cache key for settings.
     */
    private const CACHE_KEY = 'subscription_settings';

    /**
     * Get the singleton settings instance.
     */
    public static function instance(): self
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            return static::firstOrCreate([], [
                'trial_duration_days' => 30,
                'grace_period_days' => 7,
                'enable_trial' => true,
                'require_trial_verification' => true,
                'auto_renew_default' => false,
            ]);
        });
    }

    /**
     * Clear cached settings.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Override save to clear cache.
     */
    public function save(array $options = []): bool
    {
        $result = parent::save($options);
        self::clearCache();

        return $result;
    }

    /**
     * Get trial duration days.
     */
    public static function getTrialDays(): int
    {
        return self::instance()->trial_duration_days;
    }

    /**
     * Get grace period days.
     */
    public static function getGracePeriodDays(): int
    {
        return self::instance()->grace_period_days;
    }

    /**
     * Check if trial is enabled.
     */
    public static function isTrialEnabled(): bool
    {
        return self::instance()->enable_trial;
    }

    /**
     * Check if trial verification is required.
     */
    public static function isTrialVerificationRequired(): bool
    {
        return self::instance()->require_trial_verification;
    }
}
