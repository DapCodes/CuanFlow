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
        // Global middleware: block banned IPs
        $middleware->prepend(\App\Http\Middleware\CheckBannedIp::class);

        // Append activity log context middleware to web group
        $middleware->appendToGroup('web', \App\Http\Middleware\LogActivityContext::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackUserPresence::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckUserActive::class);

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
            'admin.redirect' => \App\Http\Middleware\RedirectAdmin::class,
            'check.maintenance' => \App\Http\Middleware\CheckMaintenance::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Archive old activity logs every Sunday at 2 AM
        $schedule->command('log:archive')->weeklyOn(0, '02:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
