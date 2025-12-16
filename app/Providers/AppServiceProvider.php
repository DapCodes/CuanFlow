<?php

namespace App\Providers;

use App\Services\ClaraAiService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

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
    }
}
