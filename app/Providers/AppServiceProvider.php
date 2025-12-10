<?php

namespace App\Providers;

use App\Services\ClaraAiService;
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
        //
    }
}
