<?php

namespace App\Providers;

use App\Services\ClaraAiService;
use Illuminate\Auth\Notifications\VerifyEmail;
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
