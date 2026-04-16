<?php

namespace App\Providers;

use App\Listeners\LogFailedLogin;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Listeners\UserPresenceSubscriber;
use App\Models\Activity;
use App\Services\ClaraAiService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Blade;
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
        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Logout::class, LogSuccessfulLogout::class);
        Event::listen(Failed::class, LogFailedLogin::class);
        Event::subscribe(UserPresenceSubscriber::class);

        // Inject context metadata into every activity log entry
        Activity::saving(function (Activity $activity) {
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
        Blade::if('hasSubscription', function () {
            return auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasActiveSubscription());
        });

        // Blade directive: Check feature access
        Blade::if('canAccessFeature', function ($featureName) {
            return auth()->check() && auth()->user()->canAccessFeature($featureName);
        });

        // Blade directive: Check outlet limit
        Blade::if('canCreateOutlet', function () {
            return auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->canCreateOutlet());
        });
    }
}
