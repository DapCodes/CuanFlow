<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/payment/midtrans/notification', [PaymentController::class, 'handleMidtransNotification'])
    ->name('payment.midtrans.notification');