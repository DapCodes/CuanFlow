<?php

use App\Http\Controllers\AiInsightController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\ChangeOutletController;
use App\Http\Controllers\ClaraAiController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OutletInformationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PointOfSaleController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RawMaterialAndSupplierController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RegisterOutletController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [MenuController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::prefix('outlets/register')->name('outlets.register.')->group(function () {
        Route::get('/', [RegisterOutletController::class, 'index'])->name('index');
        Route::post('/', [RegisterOutletController::class, 'store'])->name('store');
    });

    Route::post('/change-outlet', [ChangeOutletController::class, 'switch'])
        ->name('change.outlet')
        ->middleware('auth');

    Route::resource('outlets', OutletInformationController::class);
    Route::post('outlets/{outlet}/toggle-status', [OutletInformationController::class, 'toggleStatus'])
        ->name('outlets.toggle-status');

    // routes/web.php
    Route::prefix('products-hpp')->name('products-hpp.')->group(function () {
        Route::get('/generate-code', [App\Http\Controllers\ProductHppController::class, 'generateCode'])->name('generate-code');
        Route::get('/generate-barcode', [App\Http\Controllers\ProductHppController::class, 'generateBarcode'])->name('generate-barcode');
        Route::get('/ajax/raw-material-price', [App\Http\Controllers\ProductHppController::class, 'getRawMaterialPrice'])->name('ajax.raw-material-price');

        Route::get('/', [App\Http\Controllers\ProductHppController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\ProductHppController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ProductHppController::class, 'store'])->name('store');
        Route::get('/{product}', [App\Http\Controllers\ProductHppController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [App\Http\Controllers\ProductHppController::class, 'edit'])->name('edit');
        Route::put('/{product}', [App\Http\Controllers\ProductHppController::class, 'update'])->name('update');
        Route::delete('/{product}', [App\Http\Controllers\ProductHppController::class, 'destroy'])->name('destroy');

        Route::get('/sales-analytics', [App\Http\Controllers\ProductHppController::class, 'getSalesAnalytics'])
            ->name('sales-analytics');

        Route::post('/{product}/toggle-status', [App\Http\Controllers\ProductHppController::class, 'toggleStatus'])
            ->name('toggle-status');

        Route::post('/generate-recipe-ai', [App\Http\Controllers\ProductHppController::class, 'generateRecipeAI'])
            ->name('generate-recipe-ai');
    });

    Route::prefix('raw-materials')->name('raw-materials.')->group(function () {
        Route::get('/', [RawMaterialAndSupplierController::class, 'indexRawMaterial'])
            ->name('index');

        Route::get('/suppliers', [RawMaterialAndSupplierController::class, 'indexSupplier'])
            ->name('suppliers');

        Route::get('/create', [RawMaterialAndSupplierController::class, 'createRawMaterial'])
            ->name('create');
        Route::post('/', [RawMaterialAndSupplierController::class, 'storeRawMaterial'])
            ->name('store');
        Route::get('/{rawMaterial}', [RawMaterialAndSupplierController::class, 'showRawMaterial'])
            ->name('show');
        Route::get('/{rawMaterial}/edit', [RawMaterialAndSupplierController::class, 'editRawMaterial'])
            ->name('edit');
        Route::put('/{rawMaterial}', [RawMaterialAndSupplierController::class, 'updateRawMaterial'])
            ->name('update');
        Route::delete('/{rawMaterial}', [RawMaterialAndSupplierController::class, 'destroyRawMaterial'])
            ->name('destroy');

        // Route untuk kelola stok
        Route::get('/{rawMaterial}/manage-stock', [RawMaterialAndSupplierController::class, 'manageStock'])
            ->name('manage-stock');
        Route::post('/{rawMaterial}/update-stock', [RawMaterialAndSupplierController::class, 'updateStock'])
            ->name('update-stock');
        Route::get('/{rawMaterial}/stock-history', [RawMaterialAndSupplierController::class, 'stockHistory'])
            ->name('stock-history');

        // Route untuk supplier CRUD
        Route::get('/suppliers/create', [RawMaterialAndSupplierController::class, 'createSupplier'])
            ->name('suppliers.create');
        Route::post('/suppliers', [RawMaterialAndSupplierController::class, 'storeSupplier'])
            ->name('suppliers.store');
        Route::get('/suppliers/{supplier}', [RawMaterialAndSupplierController::class, 'showSupplier'])
            ->name('suppliers.show');
        Route::get('/suppliers/{supplier}/edit', [RawMaterialAndSupplierController::class, 'editSupplier'])
            ->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [RawMaterialAndSupplierController::class, 'updateSupplier'])
            ->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [RawMaterialAndSupplierController::class, 'destroySupplier'])
            ->name('suppliers.destroy');
    });

    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/', [ProductionController::class, 'index'])->name('index');
        Route::get('/create', [ProductionController::class, 'create'])->name('create');
        Route::post('/', [ProductionController::class, 'store'])->name('store');
        Route::get('/{production}', [ProductionController::class, 'show'])->name('show');
        Route::post('/{production}/start', [ProductionController::class, 'start'])->name('start');
        Route::post('/{production}/complete', [ProductionController::class, 'complete'])->name('complete');
        Route::post('/{production}/cancel', [ProductionController::class, 'cancel'])->name('cancel');
        Route::get('/api/recipe-details/{product}', [ProductionController::class, 'getRecipeDetails'])->name('recipe-details');
    });

    Route::get('/api/sale/{sale}', [SaleController::class, 'showJson'])->name('sale.api.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('sales/daily', [SaleController::class, 'daily'])->name('sales.daily');
    Route::post('sales/{sale}/refund', [SaleController::class, 'refund'])->name('sales.refund');
    Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('ai-insights')->name('ai-insights.')->group(function () {
        Route::get('/', [AiInsightController::class, 'index'])->name('index');
        Route::get('/{id}', [AiInsightController::class, 'show'])->name('show');
        Route::post('/{id}/read', [AiInsightController::class, 'markAsRead'])->name('mark-read');
        Route::post('/{id}/dismiss', [AiInsightController::class, 'dismiss'])->name('dismiss');
        Route::post('/mark-all-read', [AiInsightController::class, 'markAllAsRead'])->name('mark-all-read');
    });
});

Route::middleware('auth')->prefix('clara-ai')->name('clara-ai.')->group(function () {
    Route::get('/', [ClaraAiController::class, 'index'])->name('index');
    Route::post('/chat', [ClaraAiController::class, 'chat'])->name('chat');
    Route::get('/new-session', [ClaraAiController::class, 'newSession'])->name('new-session');
    Route::delete('/session/{id}', [ClaraAiController::class, 'deleteSession'])->name('delete-session');
});

// POS Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/pos', [PointOfSaleController::class, 'index'])->name('pos.index');
    Route::get('/pos/check-register', [PointOfSaleController::class, 'checkCashRegister'])->name('cash-register.check');
    Route::post('/pos/start-register', [PointOfSaleController::class, 'startCashRegister'])->name('cash-register.start');
    Route::get('/cash-register/check-sales', [PointOfSaleController::class, 'checkSales'])->name('cash-register.check-sales');
    Route::post('/cash-register/close-silent', [PointOfSaleController::class, 'closeSilent'])->name('cash-register.close-silent');
    Route::post('/pos/cart/add', [PointOfSaleController::class, 'addToCart'])->name('pos.cart.add');
    Route::post('/pos/cart/update', [PointOfSaleController::class, 'updateCartItem'])->name('pos.cart.update');
    Route::delete('/pos/cart/remove', [PointOfSaleController::class, 'removeCartItem'])->name('pos.cart.remove');
    Route::post('/pos/cart/clear', [PointOfSaleController::class, 'clearCart'])->name('pos.cart.clear');
    Route::post('/pos/customer/set', [PointOfSaleController::class, 'setCustomer'])->name('pos.customer.set');

    Route::post('/cash-register/set-opening-amount', [PointOfSaleController::class, 'setOpeningAmount'])
        ->name('cash-register.set-opening-amount');

    Route::get('/cash-register/close', [CashRegisterController::class, 'showClosePage'])->name('cash-register.close');
    Route::post('/cash-register/process-close', [CashRegisterController::class, 'processClose'])->name('cash-register.process-close');
    Route::get('/cash-register/history', [CashRegisterController::class, 'history'])->name('cash-register.history');
    Route::get('/cash-register/{id}', [CashRegisterController::class, 'show'])->name('cash-register.show');

    Route::post('/payment/cash', [PaymentController::class, 'processCashPayment'])->name('payment.cash');
    Route::post('/payment/transfer', [PaymentController::class, 'processTransferPayment'])->name('payment.transfer');
    Route::post('/payment/midtrans/token', [PaymentController::class, 'createMidtransToken'])->name('payment.midtrans.token');
    Route::post('/payment/midtrans/notification', [PaymentController::class, 'handleMidtransNotification'])->name('payment.midtrans.notification');
    Route::get('/payment/midtrans/finish', [PaymentController::class, 'midtransFinish'])->name('payment.midtrans.finish');

    Route::prefix('discounts')->name('discounts.')->group(function () {
        Route::get('/generate-code', [App\Http\Controllers\DiscountController::class, 'generateCode'])->name('generate-code');
        Route::get('/', [App\Http\Controllers\DiscountController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\DiscountController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\DiscountController::class, 'store'])->name('store');
        Route::get('/{discount}', [App\Http\Controllers\DiscountController::class, 'show'])->name('show');
        Route::get('/{discount}/edit', [App\Http\Controllers\DiscountController::class, 'edit'])->name('edit');
        Route::put('/{discount}', [App\Http\Controllers\DiscountController::class, 'update'])->name('update');
        Route::delete('/{discount}', [App\Http\Controllers\DiscountController::class, 'destroy'])->name('destroy');
        Route::post('/{discount}/toggle-status', [App\Http\Controllers\DiscountController::class, 'toggleStatus'])->name('toggle-status');
    });

    Route::get('/api/sale/{id}', function ($id) {
        $sale = \App\Models\Sale::with('items')->findOrFail($id);

        return response()->json($sale);
    });

    Route::get('/receipt/print/{id}', [ReceiptController::class, 'print'])->name('receipt.print');
    Route::get('/receipt/download/{id}', [ReceiptController::class, 'download'])->name('receipt.download');
});

// Payment Routes
Route::middleware(['auth'])->prefix('payment')->name('payment.')->group(function () {
    Route::post('/cash', [PaymentController::class, 'processCashPayment'])->name('cash');
    Route::post('/transfer', [PaymentController::class, 'processTransferPayment'])->name('transfer');
    Route::post('/midtrans/token', [PaymentController::class, 'createMidtransToken'])->name('midtrans.token');
    Route::get('/midtrans/finish', [PaymentController::class, 'midtransFinish'])->name('midtrans.finish');
});

Route::middleware(['auth'])->prefix('sales')->name('sales.')->group(function () {
    Route::get('/', [SaleController::class, 'index'])->name('index');
    Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
    Route::get('/{sale}/print', [SaleController::class, 'printReceipt'])->name('print');
    Route::post('/{sale}/refund', [SaleController::class, 'refund'])->name('refund');
    Route::get('/daily', [SaleController::class, 'daily'])->name('daily');
});

Route::middleware(['auth'])->prefix('receipt')->name('receipt.')->group(function () {
    Route::get('print/{id}', [ReceiptController::class, 'printReceipt'])->name('print');
    Route::get('download/{id}', [ReceiptController::class, 'downloadReceipt'])->name('download');
    Route::get('preview/{id}', [ReceiptController::class, 'previewReceipt'])->name('preview');
});

Route::middleware(['auth'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/', [FinanceController::class, 'index'])->name('index');
    Route::get('/income/create', [FinanceController::class, 'createIncome'])->name('income.create');
    Route::post('/income', [FinanceController::class, 'storeIncome'])->name('income.store');
    Route::get('/expense/create', [FinanceController::class, 'createExpense'])->name('expense.create');
    Route::post('/expense', [FinanceController::class, 'storeExpense'])->name('expense.store');
    Route::post('/validate-revenue', [FinanceController::class, 'validateRevenue'])->name('validate-revenue');
    Route::get('/daily', [FinanceController::class, 'daily'])->name('daily');

    // Chart API Routes (BARU)
    Route::get('/revenue-chart', [FinanceController::class, 'getRevenueChart'])->name('revenue-chart');
    Route::get('/expense-chart', [FinanceController::class, 'getExpenseChart'])->name('expense-chart');
});
require __DIR__.'/auth.php';
