<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('admin')
                ->middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['api', 'auth:sanctum']],
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Exclude CSRF untuk route Midtrans notification
        $middleware->validateCsrfTokens(except: [
            'api/*', // Semua route API
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'subscription.check' => \App\Http\Middleware\CheckSubscription::class,
            'feature.access' => \App\Http\Middleware\CheckFeatureAccess::class,
            'limit.outlet' => \App\Http\Middleware\CheckOutletLimit::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Schedule sudah didefinisikan di routes/console.php
        // Tapi bisa juga ditambahkan di sini jika diperlukan
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
