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
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OutletPaymentLinkController;
use App\Http\Controllers\OutletPolicyController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Email Verification untuk WEB
Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed'])
    ->name('verification.verify');


Route::get('/dashboard', [MenuController::class, 'index'])
    ->middleware(['auth', 'verified', \App\Http\Middleware\TriggerInsightOnOnline::class])
    ->name('dashboard');

// Public Receipt Route
Route::get('/receipts/{invoice}', [ReceiptController::class, 'show'])->name('receipts.show');

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
        Route::get('/{product}/barcode-preview', [App\Http\Controllers\ProductHppController::class, 'barcodePreview'])->name('barcode-preview');
        Route::get('/{product}/barcode-download', [App\Http\Controllers\ProductHppController::class, 'barcodeDownload'])->name('barcode-download');

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
    Route::post('/{production}/remove-expired', [ProductionController::class, 'removeExpired'])->name('remove-expired');
    Route::get('/api/recipe-details/{product}', [ProductionController::class, 'getRecipeDetails'])->name('recipe-details');
    
    Route::get('/stock/{product}', [ProductionController::class, 'showStock'])->name('stock.show');
    Route::post('/stock/{product}/remove-expired', [ProductionController::class, 'removeExpiredStock'])->name('stock.remove-expired');
});

    Route::get('/api/sale/{sale}', [SaleController::class, 'showJson'])->name('sale.api.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('sales/daily', [SaleController::class, 'daily'])->name('sales.daily');
    Route::post('sales/{sale}/refund', [SaleController::class, 'refund'])->name('sales.refund');
    Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

    Route::prefix('debt')->name('debt.')->group(function () {
        Route::get('/search-customer', [App\Http\Controllers\DebtPaymentController::class, 'searchCustomer'])
            ->name('search-customer');
        Route::post('/process', [App\Http\Controllers\DebtPaymentController::class, 'processDebtPayment'])
            ->name('process');
    });

    // Customer & Piutang Management
    Route::prefix('customer-debts')->name('customer-debts.')->group(function () {
        Route::get('/', [App\Http\Controllers\CustomerDebtController::class, 'index'])->name('index');
        Route::get('/customers', [App\Http\Controllers\CustomerDebtController::class, 'getCustomers'])->name('customers');
        Route::get('/debts', [App\Http\Controllers\CustomerDebtController::class, 'getDebts'])->name('debts');
        Route::get('/{debt}/detail', [App\Http\Controllers\CustomerDebtController::class, 'getDebtDetail'])->name('detail');
        Route::post('/{debt}/pay', [App\Http\Controllers\CustomerDebtController::class, 'payDebt'])->name('pay');
        Route::post('/{debt}/midtrans-token', [App\Http\Controllers\CustomerDebtController::class, 'createMidtransToken'])->name('midtrans-token');
    });
    
    Route::post('/payment/check-amount', [PaymentController::class, 'checkPaymentAmount'])
        ->name('payment.check-amount');

    // Employee CRUD
    Route::resource('employees', EmployeeController::class);
    
    // Toggle Employee Status (Active/Inactive)
    Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])
        ->name('employees.toggle-status');
    
    // Resend Verification Email
    Route::post('employees/{employee}/resend-verification', [EmployeeController::class, 'resendVerification'])
        ->name('employees.resend-verification');


});

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('ai-insights')->name('ai-insights.')->group(function () {
        Route::get('/', [AiInsightController::class, 'index'])->name('index');
        Route::get('/{id}', [AiInsightController::class, 'show'])->name('show');
        Route::post('/{id}/read', [AiInsightController::class, 'markAsRead'])->name('mark-read');
        Route::post('/{id}/dismiss', [AiInsightController::class, 'dismiss'])->name('dismiss');
        Route::post('/mark-all-read', [AiInsightController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/calendar/summary', [AiInsightController::class, 'calendarSummary'])->name('calendar.summary');
        Route::get('/calendar/daily', [AiInsightController::class, 'daily'])->name('calendar.daily');
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

    Route::post('/pos/products/{product}/toggle-visibility', [PointOfSaleController::class, 'toggleProductVisibility'])
        ->name('pos.products.toggle-visibility');

    Route::post('/cash-register/set-opening-amount', [PointOfSaleController::class, 'setOpeningAmount'])
        ->name('cash-register.set-opening-amount');

    Route::get('/cash-register/close', [CashRegisterController::class, 'showClosePage'])->name('cash-register.close');
    Route::post('/cash-register/process-close', [CashRegisterController::class, 'processClose'])->name('cash-register.process-close');
    Route::get('/cash-register/history', [CashRegisterController::class, 'history'])->name('cash-register.history');
    Route::get('/cash-register/{id}', [CashRegisterController::class, 'show'])->name('cash-register.show');

    Route::post('/payment/cash', [PaymentController::class, 'processCashPayment'])->name('payment.cash');
    Route::post('/payment/transfer', [PaymentController::class, 'processTransferPayment'])->name('payment.transfer');
    Route::post('/payment/midtrans/token', [PaymentController::class, 'createMidtransToken'])->name('payment.midtrans.token');
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

Route::middleware(['auth'])->prefix('pos/discounts')->name('pos.discounts.')->group(function () {
    Route::post('/apply', [App\Http\Controllers\PosDiscountController::class, 'apply'])->name('apply');
    Route::post('/assign-free-items', [App\Http\Controllers\PosDiscountController::class, 'assignFreeItems'])->name('assign-free-items');
    Route::post('/clear', [App\Http\Controllers\PosDiscountController::class, 'clear'])->name('clear');
    Route::get('/available', [App\Http\Controllers\PosDiscountController::class, 'available'])->name('available');
});

Route::middleware(['auth'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/', [FinanceController::class, 'index'])->name('index');
    
    Route::get('/income/create', [FinanceController::class, 'createIncome'])->name('income.create');
    Route::post('/income', [FinanceController::class, 'storeIncome'])->name('income.store');
    Route::get('/income/{expense}/edit', [FinanceController::class, 'editIncome'])->name('income.edit');
    Route::put('/income/{expense}', [FinanceController::class, 'updateIncome'])->name('income.update');

    Route::get('/expense/create', [FinanceController::class, 'createExpense'])->name('expense.create');
    Route::post('/expense', [FinanceController::class, 'storeExpense'])->name('expense.store');
    Route::get('/expense/{expense}/edit', [FinanceController::class, 'editExpense'])->name('expense.edit');
    Route::put('/expense/{expense}', [FinanceController::class, 'updateExpense'])->name('expense.update');

    Route::delete('/{expense}', [FinanceController::class, 'destroy'])->name('destroy');
    
    // AJAX Routes (BARU untuk POS)
    Route::get('/categories-ajax', [FinanceController::class, 'getCategoriesAjax'])->name('categories.ajax');
    Route::post('/income-ajax', [FinanceController::class, 'storeIncomeAjax'])->name('income.store.ajax');
    Route::post('/expense-ajax', [FinanceController::class, 'storeExpenseAjax'])->name('expense.store.ajax');

    Route::post('/validate-revenue', [FinanceController::class, 'validateRevenue'])->name('validate-revenue');
    Route::get('/daily', [FinanceController::class, 'daily'])->name('daily');

    // Chart API Routes (BARU)
    Route::get('/revenue-chart', [FinanceController::class, 'getRevenueChart'])->name('revenue-chart');
    Route::get('/expense-chart', [FinanceController::class, 'getExpenseChart'])->name('expense-chart');
});

// Report Routes
Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('index');
    Route::get('/ajax-data', [App\Http\Controllers\ReportController::class, 'ajaxData'])->name('ajax-data');
    Route::get('/export-pdf', [App\Http\Controllers\ReportController::class, 'exportPdf'])->name('export-pdf');
    Route::get('/export-excel', [App\Http\Controllers\ReportController::class, 'exportExcel'])->name('export-excel');
});

// Statistics / Dashboard Routes
Route::middleware(['auth'])->prefix('statistics')->name('statistics.')->group(function () {
    Route::get('/', [App\Http\Controllers\StatisticsController::class, 'index'])->name('index');
    Route::get('/export', [App\Http\Controllers\StatisticsController::class, 'export'])->name('export');
    Route::get('/sales-chart', [App\Http\Controllers\StatisticsController::class, 'getSalesChart'])->name('sales-chart');
    Route::get('/payment-method-chart', [App\Http\Controllers\StatisticsController::class, 'getPaymentMethodChart'])->name('payment-method-chart');
    Route::get('/top-products-chart', [App\Http\Controllers\StatisticsController::class, 'getTopProductsChart'])->name('top-products-chart');
    Route::get('/category-chart', [App\Http\Controllers\StatisticsController::class, 'getCategoryChart'])->name('category-chart');
    Route::get('/hourly-chart', [App\Http\Controllers\StatisticsController::class, 'getHourlyChart'])->name('hourly-chart');
    Route::get('/expense-chart', [App\Http\Controllers\StatisticsController::class, 'getExpenseChart'])->name('expense-chart');
});

// FAQ Routes
Route::middleware(['auth'])->prefix('faqs')->name('faqs.')->group(function () {
    Route::get('/', [App\Http\Controllers\FaqController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\FaqController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\FaqController::class, 'store'])->name('store');
    Route::get('/{faq}', [App\Http\Controllers\FaqController::class, 'show'])->name('show');
    Route::get('/{faq}/edit', [App\Http\Controllers\FaqController::class, 'edit'])->name('edit');
    Route::put('/{faq}', [App\Http\Controllers\FaqController::class, 'update'])->name('update');
    Route::delete('/{faq}', [App\Http\Controllers\FaqController::class, 'destroy'])->name('destroy');
    Route::post('/{faq}/toggle-status', [App\Http\Controllers\FaqController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{faq}/helpful', [App\Http\Controllers\FaqController::class, 'markHelpful'])->name('helpful');
    Route::post('/{faq}/not-helpful', [App\Http\Controllers\FaqController::class, 'markNotHelpful'])->name('not-helpful');
});

Route::middleware(['auth'])->prefix('outlet-payment-links')->name('outlet-payment-links.')->group(function () {
    Route::get('/', [OutletPaymentLinkController::class, 'index'])->name('index');
    Route::get('/create', [OutletPaymentLinkController::class, 'create'])->name('create');
    Route::post('/', [OutletPaymentLinkController::class, 'store'])->name('store');
    Route::get('/{outletPaymentLink}', [OutletPaymentLinkController::class, 'show'])->name('show');
    Route::get('/{outletPaymentLink}/edit', [OutletPaymentLinkController::class, 'edit'])->name('edit');
    Route::put('/{outletPaymentLink}', [OutletPaymentLinkController::class, 'update'])->name('update');
    Route::delete('/{outletPaymentLink}', [OutletPaymentLinkController::class, 'destroy'])->name('destroy');
    Route::post('/{outletPaymentLink}/toggle-status', [OutletPaymentLinkController::class, 'toggleStatus'])->name('toggle-status');
});


// Stock Opname Routes
Route::middleware(['auth'])->prefix('stock-opname')->name('stock-opname.')->group(function () {
    Route::get('/', [App\Http\Controllers\StockOpnameController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\StockOpnameController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\StockOpnameController::class, 'store'])->name('store');
    Route::get('/{stockOpname}', [App\Http\Controllers\StockOpnameController::class, 'show'])->name('show');
    Route::put('/{stockOpname}', [App\Http\Controllers\StockOpnameController::class, 'update'])->name('update');
    Route::post('/{stockOpname}/finalize', [App\Http\Controllers\StockOpnameController::class, 'finalize'])->name('finalize');
    Route::delete('/{stockOpname}', [App\Http\Controllers\StockOpnameController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('outlet-policies', OutletPolicyController::class);
});

require __DIR__.'/auth.php';
