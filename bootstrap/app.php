<?php

use App\Http\Middleware\CheckBannedIp;
use App\Http\Middleware\CheckFeatureAccess;
use App\Http\Middleware\CheckMaintenance;
use App\Http\Middleware\CheckOutletLimit;
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\CheckUserActive;
use App\Http\Middleware\LogActivityContext;
use App\Http\Middleware\RedirectAdmin;
use App\Http\Middleware\TrackUserPresence;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

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
        $middleware->prepend(CheckBannedIp::class);

        // Append activity log context middleware to web group
        $middleware->appendToGroup('web', LogActivityContext::class);
        $middleware->appendToGroup('web', TrackUserPresence::class);
        $middleware->appendToGroup('web', CheckUserActive::class);

        // Exclude CSRF untuk route Midtrans notification
        $middleware->validateCsrfTokens(except: [
            'api/*', // Semua route API
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'subscription.check' => CheckSubscription::class,
            'feature.access' => CheckFeatureAccess::class,
            'limit.outlet' => CheckOutletLimit::class,
            'admin.redirect' => RedirectAdmin::class,
            'check.maintenance' => CheckMaintenance::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Archive old activity logs every Sunday at 2 AM
        $schedule->command('log:archive')->weeklyOn(0, '02:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
