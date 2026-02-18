<?php

namespace App\Providers;

use App\Services\ClaraAiService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind ClaraAiService sebagai singleton
        $this->app->singleton(ClaraAiService::class, function ($app) {
            return new ClaraAiService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register auth event listeners for activity logging
        Event::listen(\Illuminate\Auth\Events\Login::class, \App\Listeners\LogSuccessfulLogin::class);
        Event::listen(\Illuminate\Auth\Events\Logout::class, \App\Listeners\LogSuccessfulLogout::class);
        Event::listen(\Illuminate\Auth\Events\Failed::class, \App\Listeners\LogFailedLogin::class);

        // Inject context metadata into every activity log entry
        \App\Models\Activity::saving(function (\App\Models\Activity $activity) {
            if (app()->bound('activitylog.context')) {
                $context = app('activitylog.context');
                $activity->ip_address = $activity->ip_address ?? ($context['ip_address'] ?? null);
                $activity->user_agent = $activity->user_agent ?? ($context['user_agent'] ?? null);
                $activity->url = $activity->url ?? ($context['url'] ?? null);
                $activity->outlet_id = $activity->outlet_id ?? ($context['outlet_id'] ?? null);
            }
        });
        VerifyEmail::createUrlUsing(function ($notifiable) {
            return URL::temporarySignedRoute(
                'api.verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });

        // Blade directive: Check if user has active subscription
        \Illuminate\Support\Facades\Blade::if('hasSubscription', function () {
            return auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasActiveSubscription());
        });

        // Blade directive: Check feature access
        \Illuminate\Support\Facades\Blade::if('canAccessFeature', function ($featureName) {
            return auth()->check() && auth()->user()->canAccessFeature($featureName);
        });

        // Blade directive: Check outlet limit
        \Illuminate\Support\Facades\Blade::if('canCreateOutlet', function () {
            return auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->canCreateOutlet());
        });
    }
}
