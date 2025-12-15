<?php

use App\Http\Controllers\Api\OutletApiController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/outlets', [OutletApiController::class, 'index']); // list + search
    Route::get('/outlets/{outlet}', [OutletApiController::class, 'show']); // detail
});

Route::post('/payment/midtrans/notification', [PaymentController::class, 'handleMidtransNotification'])
    ->name('payment.midtrans.notification');
