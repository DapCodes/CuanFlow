<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ClaraAiService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind ClaraAiService sebagai singleton
        $this->app->singleton(ClaraAiService::class, function ($app) {
            return new ClaraAiService();
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