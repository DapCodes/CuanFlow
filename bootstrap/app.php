<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Exclude CSRF untuk route Midtrans notification
        $middleware->validateCsrfTokens(except: [
            'api/*', // Semua route API
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Schedule sudah didefinisikan di routes/console.php
        // Tapi bisa juga ditambahkan di sini jika diperlukan
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
