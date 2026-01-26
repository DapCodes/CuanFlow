<?php

use App\Http\Controllers\Api\Auth\EmailVerifyController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResendVerificationController;
use App\Http\Controllers\Api\OutletApiController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerifyEmailController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [RegisterController::class, 'register']);
    Route::post('/auth/login', [LoginController::class, 'login']);
    Route::get('/email/verify/{id}/{hash}', EmailVerifyController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('api.verification.verify');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [LogoutController::class, 'logout']);
        Route::get('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'show']);
        Route::post('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
        Route::post('/email/verification-notification', [ResendVerificationController::class, 'send'])
            ->middleware('throttle:6,1')
            ->name('api.verification.send');
    });
    Route::get('/outlets', [OutletApiController::class, 'index']);
    Route::get('/outlets/{outlet}', [OutletApiController::class, 'show']);
});

Route::post('/payment/midtrans/notification', [PaymentController::class, 'handleMidtransNotification'])
    ->name('payment.midtrans.notification');