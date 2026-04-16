<?php

use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\Auth\EmailVerifyController;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResendVerificationController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\DebtPaymentApiController;
use App\Http\Controllers\Api\HeatmapController;
use App\Http\Controllers\Api\MobileCashFlowController;
use App\Http\Controllers\Api\OutletApiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ResellerApplicationApiController;
use App\Http\Controllers\Api\VoucherClaimController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [RegisterController::class, 'register']);
    Route::post('/auth/login', [LoginController::class, 'login']);

    // Google Auth
    Route::post('/auth/google/login', [GoogleAuthController::class, 'login']);
    Route::post('/auth/google/register', [GoogleAuthController::class, 'register']);

    Route::get('/email/verify/{id}/{hash}', EmailVerifyController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('api.verification.verify');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [LogoutController::class, 'logout']);
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::post('/profile', [ProfileController::class, 'update']);
        Route::post('/email/verification-notification', [ResendVerificationController::class, 'send'])
            ->middleware('throttle:6,1')
            ->name('api.verification.send');

        // Google Bind/Unlink
        Route::post('/auth/google/bind', [GoogleAuthController::class, 'bind']);
        Route::post('/auth/google/unlink', [GoogleAuthController::class, 'unlink']);

        // Reseller Applications
        Route::get('/reseller-applications', [ResellerApplicationApiController::class, 'index']);
        Route::post('/reseller-applications', [ResellerApplicationApiController::class, 'store']);
        Route::get('/reseller-applications/{id}', [ResellerApplicationApiController::class, 'show']);

        // Customer Data
        Route::get('/customer/purchases', [CustomerApiController::class, 'purchases']);
        Route::get('/customer/debts', [CustomerApiController::class, 'debts']);

        // Debt Payment API
        Route::get('/debts/{id}', [DebtPaymentApiController::class, 'show']);
        Route::post('/debts/{id}/pay', [DebtPaymentApiController::class, 'pay']);
        Route::post('/debts/{id}/midtrans-token', [DebtPaymentApiController::class, 'createMidtransToken']);

        Route::post('/vouchers/claim', [VoucherClaimController::class, 'claim']);
        Route::get('/vouchers/my-vouchers', [VoucherClaimController::class, 'myVouchers']);

        // Mobile Cash Flow
        Route::apiResource('mobile-cash-flow', MobileCashFlowController::class);

        // Telegram Account Linking
        Route::get('/telegram/token', [TelegramController::class, 'generateLinkToken']);
    });

    // Voucher Claims
    Route::get('/vouchers/available', [VoucherClaimController::class, 'availableVouchers']);

    Route::get('/outlets', [OutletApiController::class, 'index']);
    Route::get('/outlets/{outlet}', [OutletApiController::class, 'show']);

    // Heatmap (public, no auth required)
    Route::prefix('heatmap')->group(function () {
        Route::get('/', [HeatmapController::class, 'heatmap']);
        Route::get('/stats', [HeatmapController::class, 'stats']);
    });
    Route::get('/business-points', [HeatmapController::class, 'businessPoints']);

    // Advertisements (Public)
    Route::get('/advertisements', [AdvertisementController::class, 'index']);
    Route::get('/advertisements/{id}', [AdvertisementController::class, 'show']);
});

Route::post('/payment/midtrans/notification', [PaymentController::class, 'handleMidtransNotification'])
    ->name('payment.midtrans.notification');

// =========================================================================
// Telegram Bot Webhook
// =========================================================================
Route::post('/telegram/webhook', [TelegramController::class, 'handleWebhook'])
    ->name('telegram.webhook');

// Telegram webhook management (protected, for admin use)
Route::prefix('telegram')->group(function () {
    Route::get('/set-webhook', [TelegramController::class, 'setWebhook'])
        ->name('telegram.set-webhook');
    Route::get('/remove-webhook', [TelegramController::class, 'removeWebhook'])
        ->name('telegram.remove-webhook');
    Route::get('/webhook-info', [TelegramController::class, 'getWebhookInfo'])
        ->name('telegram.webhook-info');
});
