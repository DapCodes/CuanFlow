<?php

use App\Http\Controllers\AiInsightController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\ChangeOutletController;
use App\Http\Controllers\ClaraAiController;
use App\Http\Controllers\CustomerDebtController;
use App\Http\Controllers\DebtPaymentController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FlowLandingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\Main\StockTransferController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OutletInformationController;
use App\Http\Controllers\OutletPaymentLinkController;
use App\Http\Controllers\OutletPolicyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PointOfSaleController;
use App\Http\Controllers\PosDiscountController;
use App\Http\Controllers\ProductHppController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RawMaterialAndSupplierController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RegisterOutletController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResellerApplicationController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPaymentController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskLabelController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\WithdrawController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// PUBLIC ROUTES
// =========================================================================

Route::get('/', function () {
    return redirect()->route('login');
});

// Landing Page
Route::get('/flow', [FlowLandingController::class, 'index'])->name('flow.index');
Route::get('/flow/{slug}', [FlowLandingController::class, 'show'])->name('flow.show');

// Public Store Pages
Route::get('/store/{id}/analytics', [LandingPageController::class, 'getAnalytics'])->name('landing-pages.analytics');
Route::get('/store/{id}/{slug?}', [LandingPageController::class, 'show'])->name('landing-pages.show');
Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');

// Legal & Receipts
Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/receipts/{invoice}', [ReceiptController::class, 'show'])->name('receipts.show');

require __DIR__.'/auth.php';

// =========================================================================
// PROTECTED ROUTES (Auth & Verified)
// =========================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/employee-locked', function () {
        return view('subscription.employee_locked');
    })->name('employee.locked');
});

Route::middleware(['auth', 'verified', 'subscription.check'])->group(function () {

    // ---------------------------------------------------------------------
    // Subscription & Billing
    // ---------------------------------------------------------------------
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::post('/select-plan', [SubscriptionController::class, 'selectPlan'])->name('select-plan');
        Route::get('/payment', [SubscriptionPaymentController::class, 'show'])->name('payment');
        Route::get('/payment/finish', [SubscriptionPaymentController::class, 'finish'])->name('payment.finish');
        Route::get('/payment/error', [SubscriptionPaymentController::class, 'error'])->name('payment.error');

        // Trial
        Route::post('/trial/request', [SubscriptionController::class, 'requestTrial'])->name('trial.request');
        Route::get('/trial/verification', [SubscriptionController::class, 'createTrialVerification'])->name('trial-verification');
        Route::post('/trial/verification', [SubscriptionController::class, 'storeTrialVerification'])->name('trial-verification.store');

        // Show modal via AJAX
        Route::post('/show-modal', function () {
            session(['show_subscription_modal' => true, 'subscription_modal_reason' => 'user_requested']);

            return response()->json(['success' => true]);
        })->name('show-modal');

        // Clear modal via AJAX (for re-explore tour)
        Route::post('/clear-modal', function () {
            session()->forget(['show_subscription_modal', 'subscription_modal_reason', 'force_subscription_choice']);
            session(['show_welcome_tour' => true]);

            return response()->json(['success' => true]);
        })->name('clear-modal');

        // Destroy onboarding flag via AJAX (when user chooses trial/buy)
        Route::post('/destroy-onboarding-flag', function () {
            session()->forget(['new_user_onboarding', 'force_subscription_choice']);

            return response()->json(['success' => true]);
        })->name('destroy-onboarding-flag');

        // Manage Subscription
        Route::get('/manage', [\App\Http\Controllers\SubscriptionManagementController::class, 'index'])->name('manage');
        Route::post('/manage/add-duration', [\App\Http\Controllers\SubscriptionManagementController::class, 'addDuration'])->name('manage.add-duration');
        Route::post('/manage/upgrade', [\App\Http\Controllers\SubscriptionManagementController::class, 'upgrade'])->name('manage.upgrade');
    });

    // ---------------------------------------------------------------------
    // Dashboard
    // ---------------------------------------------------------------------
    Route::get('/dashboard', [MenuController::class, 'index'])
        ->middleware([\App\Http\Middleware\TriggerInsightOnOnline::class])
        ->name('dashboard');

    // ---------------------------------------------------------------------
    // User Profile
    // ---------------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ---------------------------------------------------------------------
    // Outlet Management
    // ---------------------------------------------------------------------
    Route::prefix('outlets')->name('outlets.')->group(function () {
        // Register Outlet
        Route::prefix('register')->name('register.')->group(function () {
            Route::get('/', [RegisterOutletController::class, 'index'])->name('index');
            Route::post('/', [RegisterOutletController::class, 'store'])->name('store');
        });

        // Custom Actions
        Route::post('{outlet}/toggle-status', [OutletInformationController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Switch Outlet
    Route::post('/change-outlet', [ChangeOutletController::class, 'switch'])->name('change.outlet');

    // Core Resources
    // Core outlet resources (multi_outlet feature for managing multiple outlets)
    Route::resource('outlets', OutletInformationController::class)->middleware('feature.access:multi_outlet');
    Route::resource('outlet-policies', OutletPolicyController::class)->middleware('feature.access:outlet_policies');

    // Outlet Payment Links
    Route::post('outlet-payment-links/{outletPaymentLink}/toggle-status', [OutletPaymentLinkController::class, 'toggleStatus'])->name('outlet-payment-links.toggle-status')->middleware('feature.access:payment_methods');
    Route::resource('outlet-payment-links', OutletPaymentLinkController::class)->middleware('feature.access:payment_methods');

    // ---------------------------------------------------------------------
    // Employee Management
    // ---------------------------------------------------------------------
    Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status')->middleware('feature.access:employee_management');
    Route::post('employees/{employee}/resend-verification', [EmployeeController::class, 'resendVerification'])->name('employees.resend-verification')->middleware('feature.access:employee_management');
    Route::resource('employees', EmployeeController::class)->middleware('feature.access:employee_management');

    // ---------------------------------------------------------------------
    // Product & Inventory Management
    // ---------------------------------------------------------------------

    // Product HPP / Menu
    Route::prefix('products-hpp')->name('products-hpp.')->middleware('feature.access:products_recipes')->group(function () {
        Route::get('/generate-code', [ProductHppController::class, 'generateCode'])->name('generate-code');
        Route::get('/generate-barcode', [ProductHppController::class, 'generateBarcode'])->name('generate-barcode');
        Route::get('/ajax/raw-material-price', [ProductHppController::class, 'getRawMaterialPrice'])->name('ajax.raw-material-price');
        Route::get('/sales-analytics', [ProductHppController::class, 'getSalesAnalytics'])->name('sales-analytics');
        Route::post('/generate-recipe-ai', [ProductHppController::class, 'generateRecipeAI'])->name('generate-recipe-ai');
        Route::get('/{product}/barcode-preview', [ProductHppController::class, 'barcodePreview'])->name('barcode-preview');
        Route::get('/{product}/barcode-download', [ProductHppController::class, 'barcodeDownload'])->name('barcode-download');
        Route::post('/{product}/toggle-status', [ProductHppController::class, 'toggleStatus'])->name('toggle-status');
    });
    Route::resource('products-hpp', ProductHppController::class)->parameters(['products-hpp' => 'product'])->middleware('feature.access:products_recipes');

    // Raw Materials
    Route::prefix('raw-materials')->name('raw-materials.')->middleware('feature.access:raw_materials')->group(function () {
        // Supplier CRUD (uses suppliers feature) - Moved up to avoid wildcard collision
        Route::middleware('feature.access:suppliers')->group(function () {
            Route::get('/suppliers', [RawMaterialAndSupplierController::class, 'indexSupplier'])->name('suppliers');
            Route::get('/suppliers/create', [RawMaterialAndSupplierController::class, 'createSupplier'])->name('suppliers.create');
            Route::post('/suppliers', [RawMaterialAndSupplierController::class, 'storeSupplier'])->name('suppliers.store');
            Route::get('/suppliers/{supplier}', [RawMaterialAndSupplierController::class, 'showSupplier'])->name('suppliers.show');
            Route::get('/suppliers/{supplier}/edit', [RawMaterialAndSupplierController::class, 'editSupplier'])->name('suppliers.edit');
            Route::put('/suppliers/{supplier}', [RawMaterialAndSupplierController::class, 'updateSupplier'])->name('suppliers.update');
            Route::delete('/suppliers/{supplier}', [RawMaterialAndSupplierController::class, 'destroySupplier'])->name('suppliers.destroy');
        });

        // Main Raw Material CRUD (Custom methods)
        Route::get('/', [RawMaterialAndSupplierController::class, 'indexRawMaterial'])->name('index');
        Route::get('/create', [RawMaterialAndSupplierController::class, 'createRawMaterial'])->name('create');
        Route::post('/', [RawMaterialAndSupplierController::class, 'storeRawMaterial'])->name('store');

        // Stock Management
        Route::get('/{rawMaterial}/manage-stock', [RawMaterialAndSupplierController::class, 'manageStock'])->name('manage-stock');
        Route::get('/{rawMaterial}/stock-show', [RawMaterialAndSupplierController::class, 'stockShow'])->name('stock-show');
        Route::post('/{rawMaterial}/update-stock', [RawMaterialAndSupplierController::class, 'updateStock'])->name('update-stock');
        Route::get('/{rawMaterial}/stock-history', [RawMaterialAndSupplierController::class, 'stockHistory'])->name('stock-history');
        Route::post('/{rawMaterial}/remove-expired', [RawMaterialAndSupplierController::class, 'removeExpired'])->name('remove-expired');

        // Wildcard routes MUST be at the end
        Route::get('/{rawMaterial}', [RawMaterialAndSupplierController::class, 'showRawMaterial'])->name('show');
        Route::get('/{rawMaterial}/edit', [RawMaterialAndSupplierController::class, 'editRawMaterial'])->name('edit');
        Route::put('/{rawMaterial}', [RawMaterialAndSupplierController::class, 'updateRawMaterial'])->name('update');
        Route::delete('/{rawMaterial}', [RawMaterialAndSupplierController::class, 'destroyRawMaterial'])->name('destroy');
    });

    // Production
    Route::prefix('production')->name('production.')->middleware('feature.access:production')->group(function () {
        Route::get('/', [ProductionController::class, 'index'])->name('index');
        Route::get('/create', [ProductionController::class, 'create'])->name('create');
        Route::post('/', [ProductionController::class, 'store'])->name('store');
        Route::post('/store-all', [ProductionController::class, 'storeAll'])->name('store-all'); // NEW ROUTE
        Route::post('/check-materials', [ProductionController::class, 'checkMaterialsAjax'])->name('check-materials');
        Route::post('/refund-sale', [ProductionController::class, 'refundSaleAjax'])->name('refund-sale');
        Route::get('/preparation/{saleItem}', [ProductionController::class, 'preparation'])->name('preparation'); // NEW ROUTE
        Route::get('/{production}', [ProductionController::class, 'show'])->name('show');
        Route::post('/{production}/start', [ProductionController::class, 'start'])->name('start');
        Route::post('/{production}/complete', [ProductionController::class, 'complete'])->name('complete');
        Route::post('/{production}/cancel', [ProductionController::class, 'cancel'])->name('cancel');
        Route::post('/{production}/remove-expired', [ProductionController::class, 'removeExpired'])->name('remove-expired');
        Route::get('/api/recipe-details/{product}', [ProductionController::class, 'getRecipeDetails'])->name('recipe-details');

        Route::get('/stock/{product}', [ProductionController::class, 'showStock'])->name('stock.show');
        Route::post('/stock/{product}/remove-expired', [ProductionController::class, 'removeExpiredStock'])->name('stock.remove-expired');
    });

    // Stock Opname
    Route::prefix('stock-opname')->name('stock-opname.')->middleware('feature.access:stock_opname')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index'])->name('index');
        Route::get('/create', [StockOpnameController::class, 'create'])->name('create');
        Route::post('/', [StockOpnameController::class, 'store'])->name('store');
        Route::get('/{stockOpname}', [StockOpnameController::class, 'show'])->name('show');
        Route::put('/{stockOpname}', [StockOpnameController::class, 'update'])->name('update');
        Route::post('/{stockOpname}/finalize', [StockOpnameController::class, 'finalize'])->name('finalize');
        Route::delete('/{stockOpname}', [StockOpnameController::class, 'destroy'])->name('destroy');
    });

    // Stock Transfer (Platinum only)
    Route::prefix('stock-transfers')->name('stock-transfers.')->middleware('feature.access:stock_transfer')->group(function () {
        Route::get('/', [StockTransferController::class, 'index'])->name('index');
        Route::get('/create', [StockTransferController::class, 'create'])->name('create');
        Route::post('/', [StockTransferController::class, 'store'])->name('store');
        Route::get('/{stockTransfer}', [StockTransferController::class, 'show'])->name('show');
        Route::post('/{stockTransfer}/send', [StockTransferController::class, 'updateStatus'])->name('send');
        Route::post('/{stockTransfer}/receive', [StockTransferController::class, 'receive'])->name('receive');
        Route::delete('/{stockTransfer}', [StockTransferController::class, 'destroy'])->name('destroy');
    });

    // ---------------------------------------------------------------------
    // POS (Point of Sales) & Transactions
    // ---------------------------------------------------------------------

    // POS Interface
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
    Route::get('/pos/customer/search', [PointOfSaleController::class, 'searchCustomers'])->name('pos.customer.search');
    Route::get('/pos/products/stocks', [PointOfSaleController::class, 'getProductStocks'])->name('pos.products.stocks');
    Route::get('/pos/pending-production', [PointOfSaleController::class, 'getPendingProductionSales'])->name('pos.pending-production');
    Route::post('/pos/notify-kitchen/{sale}', [PointOfSaleController::class, 'notifyKitchen'])->name('pos.notify-kitchen');
    Route::post('/pos/products/{product}/toggle-visibility', [PointOfSaleController::class, 'toggleProductVisibility'])->name('pos.products.toggle-visibility');

    // Cash Register Management
    Route::post('/cash-register/set-opening-amount', [PointOfSaleController::class, 'setOpeningAmount'])->name('cash-register.set-opening-amount');
    Route::get('/cash-register/close', [CashRegisterController::class, 'showClosePage'])->name('cash-register.close');
    Route::post('/cash-register/process-close', [CashRegisterController::class, 'processClose'])->name('cash-register.process-close');
    Route::get('/cash-register/history', [CashRegisterController::class, 'history'])->name('cash-register.history');
    Route::get('/cash-register/{id}', [CashRegisterController::class, 'show'])->name('cash-register.show');

    // POS Discounts
    Route::prefix('pos/discounts')->name('pos.discounts.')->group(function () {
        Route::post('/apply', [PosDiscountController::class, 'apply'])->name('apply');
        Route::post('/assign-free-items', [PosDiscountController::class, 'assignFreeItems'])->name('assign-free-items');
        Route::post('/clear', [PosDiscountController::class, 'clear'])->name('clear');
        Route::post('/remove', [PosDiscountController::class, 'remove'])->name('remove');
        Route::get('/available', [PosDiscountController::class, 'available'])->name('available');
    });

    // Discount Management (Admin/Settings)
    Route::prefix('discounts')->name('discounts.')->middleware('feature.access:discount_management')->group(function () {
        Route::get('/generate-code', [DiscountController::class, 'generateCode'])->name('generate-code');
        Route::post('/{discount}/toggle-status', [DiscountController::class, 'toggleStatus'])->name('toggle-status');
    });
    Route::resource('discounts', DiscountController::class)->middleware('feature.access:discount_management');

    // Sales (feature: sales_management)
    Route::prefix('sales')->name('sales.')->middleware('feature.access:sales_management')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/daily', [SaleController::class, 'daily'])->name('daily');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        Route::get('/{sale}/print', [SaleController::class, 'printReceipt'])->name('print');
        Route::post('/{sale}/refund', [SaleController::class, 'refund'])->name('refund');
    });
    Route::get('/api/sale/{sale}', [SaleController::class, 'showJson'])->name('sale.api.show');
    Route::get('/api/sale/{id}', function ($id) { // Helper route for JSON
        return response()->json(\App\Models\Sale::with('items')->findOrFail($id));
    });

    // Receipts
    Route::prefix('receipt')->name('receipt.')->group(function () {
        Route::get('print/{id}', [ReceiptController::class, 'printReceipt'])->name('print');
        Route::get('download/{id}', [ReceiptController::class, 'downloadReceipt'])->name('download');
        Route::get('preview/{id}', [ReceiptController::class, 'previewReceipt'])->name('preview');
        Route::match(['get', 'post'], 'invoice/{sale}/print', [InvoiceController::class, 'generate'])->name('invoice.print');
    });
    // Legacy receipt routes (keeping for compatibility)
    Route::get('/receipt/print/{id}', [ReceiptController::class, 'printReceipt'])->name('receipt.print');
    Route::get('/receipt/download/{id}', [ReceiptController::class, 'download'])->name('receipt.download');

    // Tables Management (feature: table_management)
    Route::prefix('tables')->name('tables.')->middleware('feature.access:table_management')->group(function () {
        Route::get('/generate-code', [TableController::class, 'generateCode'])->name('generate-code');
        Route::post('/{table}/toggle-status', [TableController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{table}/quick-toggle', [TableController::class, 'quickToggle'])->name('quick-toggle');
    });
    Route::resource('tables', TableController::class)->except(['show'])->middleware('feature.access:table_management');
    Route::get('/api/tables', [TableController::class, 'getTablesApi']);
    Route::post('/api/outlet/toggle-table-system', [TableController::class, 'toggleTableSystemApi']);
    Route::post('/api/outlet/toggle-auto-production', [TableController::class, 'toggleAutoProductionApi']);

    // ---------------------------------------------------------------------
    // Finance
    // ---------------------------------------------------------------------

    // Main Finance (feature: finance_management)
    Route::prefix('finance')->name('finance.')->middleware('feature.access:finance_management')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('index');

        // Income (feature: other_income)
        Route::middleware('feature.access:other_income')->group(function () {
            Route::get('/income/create', [FinanceController::class, 'createIncome'])->name('income.create');
            Route::post('/income', [FinanceController::class, 'storeIncome'])->name('income.store');
            Route::get('/income/{expense}/edit', [FinanceController::class, 'editIncome'])->name('income.edit');
            Route::put('/income/{expense}', [FinanceController::class, 'updateIncome'])->name('income.update');
        });

        // Expense (feature: operational_costs)
        Route::middleware('feature.access:operational_costs')->group(function () {
            Route::get('/expense/create', [FinanceController::class, 'createExpense'])->name('expense.create');
            Route::post('/expense', [FinanceController::class, 'storeExpense'])->name('expense.store');
            Route::get('/expense/{expense}/edit', [FinanceController::class, 'editExpense'])->name('expense.edit');
            Route::put('/expense/{expense}', [FinanceController::class, 'updateExpense'])->name('expense.update');
            Route::delete('/{expense}', [FinanceController::class, 'destroy'])->name('destroy');
        });

        // AJAX
        Route::get('/categories-ajax', [FinanceController::class, 'getCategoriesAjax'])->name('categories.ajax');
        Route::post('/income-ajax', [FinanceController::class, 'storeIncomeAjax'])->name('income.store.ajax');
        Route::post('/expense-ajax', [FinanceController::class, 'storeExpenseAjax'])->name('expense.store.ajax');
        Route::get('/sales-list-ajax', [FinanceController::class, 'getSalesListAjax'])->name('sales-list-ajax');
        Route::get('/daily-summary-ajax', [FinanceController::class, 'getDailySummaryAjax'])->name('daily-summary-ajax');
        Route::get('/expenses-list-ajax', [FinanceController::class, 'getExpensesAjax'])->name('expenses-list-ajax');
        Route::get('/revenue-chart', [FinanceController::class, 'getRevenueChart'])->name('revenue-chart');
        Route::get('/expense-chart', [FinanceController::class, 'getExpenseChart'])->name('expense-chart');

        Route::post('/validate-revenue', [FinanceController::class, 'validateRevenue'])->name('validate-revenue');
        Route::get('/daily', [FinanceController::class, 'daily'])->name('daily');
    });

    // Expenses Resource (feature: operational_costs)
    Route::prefix('expenses')->name('expenses.')->middleware('feature.access:operational_costs')->group(function () {
        Route::post('/{expense}/approve', [ExpenseController::class, 'approve'])->name('approve');
        Route::post('/{expense}/reject', [ExpenseController::class, 'reject'])->name('reject');
    });
    Route::resource('expenses', ExpenseController::class)->middleware('feature.access:operational_costs');

    // Payments
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::post('/cash', [PaymentController::class, 'processCashPayment'])->name('cash');
        Route::post('/transfer', [PaymentController::class, 'processTransferPayment'])->name('transfer');
        Route::post('/midtrans/token', [PaymentController::class, 'createMidtransToken'])->name('midtrans.token');
        Route::get('/midtrans/finish', [PaymentController::class, 'midtransFinish'])->name('midtrans.finish');
        Route::post('/check-amount', [PaymentController::class, 'checkPaymentAmount'])->name('check-amount'); // Renamed slightly for group consistency
    });
    // Legacy payment name support if needed somewhere else with full name, but prefix covers it
    // Original: payment.check-amount

    // Withdrawals (feature: balance_withdrawal)
    Route::prefix('withdraw')->name('withdraw.')->middleware('feature.access:balance_withdrawal')->group(function () {
        Route::get('/confirm-password', [WithdrawController::class, 'showConfirmPassword'])->name('confirm-password');
        Route::post('/confirm-password', [WithdrawController::class, 'confirmPassword'])->name('confirm-password.post');
        Route::get('/create', [WithdrawController::class, 'create'])->name('create');
        Route::post('/store', [WithdrawController::class, 'store'])->name('store');
        Route::get('/history', [WithdrawController::class, 'index'])->name('index');
        Route::post('/{withdrawal}/owner-approve', [WithdrawController::class, 'ownerApprove'])->name('owner-approve');
        Route::post('/{withdrawal}/owner-reject', [WithdrawController::class, 'ownerReject'])->name('owner-reject');
    });

    // Debt Management (feature: accounts_receivable)
    Route::prefix('debt')->name('debt.')->middleware('feature.access:accounts_receivable')->group(function () {
        Route::get('/search-customer', [DebtPaymentController::class, 'searchCustomer'])->name('search-customer');
        Route::post('/process', [DebtPaymentController::class, 'processDebtPayment'])->name('process');
        Route::post('/{sale}/produce', [DebtPaymentController::class, 'triggerProduction'])->name('produce');
        Route::post('/{sale}/wait', [DebtPaymentController::class, 'markAsWaiting'])->name('wait');
    });
    Route::prefix('customer-debts')->name('customer-debts.')->middleware('feature.access:accounts_receivable')->group(function () {
        Route::get('/', [CustomerDebtController::class, 'index'])->name('index');
        Route::get('/customers', [CustomerDebtController::class, 'getCustomers'])->name('customers');
        Route::get('/debts', [CustomerDebtController::class, 'getDebts'])->name('debts');
        Route::get('/{debt}/detail', [CustomerDebtController::class, 'getDebtDetail'])->name('detail');
        Route::post('/{debt}/pay', [CustomerDebtController::class, 'payDebt'])->name('pay');
        Route::post('/{debt}/midtrans-token', [CustomerDebtController::class, 'createMidtransToken'])->name('midtrans-token');
        Route::get('/{customer}/history', [CustomerDebtController::class, 'getCustomerHistory'])->name('history');
    });

    // ---------------------------------------------------------------------
    // Tasks (Kanban)
    // ---------------------------------------------------------------------
    Route::prefix('tasks')->name('tasks.')->middleware('feature.access:task_management')->group(function () {
        Route::get('/table', [TaskController::class, 'tableView'])->name('table');
        Route::get('/calendar', [TaskController::class, 'calendarView'])->name('calendar');
        Route::get('/calendar-data', [TaskController::class, 'getCalendarTasks'])->name('calendar-data');
        Route::post('/{task}/update-status', [TaskController::class, 'updateStatus'])->name('update-status');
        Route::post('/{task}/assign-users', [TaskController::class, 'assignUsers'])->name('assign-users');
        Route::get('/{task}/activities', [TaskController::class, 'getActivities'])->name('activities');
    });
    Route::resource('tasks', TaskController::class)->except(['create', 'edit'])->middleware('feature.access:task_management');
    Route::resource('task-labels', TaskLabelController::class)->middleware('feature.access:task_management');

    // ---------------------------------------------------------------------
    // Reports & Statistics
    // Reports & Statistics (feature: reports)
    Route::prefix('reports')->name('reports.')->middleware('feature.access:reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/ajax-data', [ReportController::class, 'ajaxData'])->name('ajax-data');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel');
    });

    // Invoices summary (feature: invoice_list)
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index')->middleware('feature.access:invoice_list');
    Route::get('/invoices/expense/{expense}/print', [InvoiceController::class, 'generateExpense'])->name('invoices.expense.print')->middleware('feature.access:invoice_list');

    // Statistics (feature: dashboard - uses same feature for stats)
    Route::prefix('statistics')->name('statistics.')->middleware('feature.access:dashboard')->group(function () {
        Route::get('/', [StatisticsController::class, 'index'])->name('index');
        Route::get('/export', [StatisticsController::class, 'export'])->name('export');
        Route::get('/summary', [StatisticsController::class, 'getSummaryDataAjax'])->name('summary');
        Route::get('/sales-chart', [StatisticsController::class, 'getSalesChart'])->name('sales-chart');
        Route::get('/transaction-chart', [StatisticsController::class, 'getTransactionChart'])->name('transaction-chart');
        Route::get('/payment-method-chart', [StatisticsController::class, 'getPaymentMethodChart'])->name('payment-method-chart');
        Route::get('/top-products-chart', [StatisticsController::class, 'getTopProductsChart'])->name('top-products-chart');
        Route::get('/category-chart', [StatisticsController::class, 'getCategoryChart'])->name('category-chart');
        Route::get('/hourly-chart', [StatisticsController::class, 'getHourlyChart'])->name('hourly-chart');
        Route::get('/weekly-chart', [StatisticsController::class, 'getWeeklyChart'])->name('weekly-chart');
        Route::get('/expense-chart', [StatisticsController::class, 'getExpenseChart'])->name('expense-chart');
        Route::get('/profit-chart', [StatisticsController::class, 'getProfitChart'])->name('profit-chart');
        Route::get('/expense-category-chart', [StatisticsController::class, 'getExpenseCategoryChart'])->name('expense-category-chart');
        Route::get('/cashier-performance-chart', [StatisticsController::class, 'getCashierPerformanceChart'])->name('cashier-performance-chart');
        Route::get('/top-customers-chart', [StatisticsController::class, 'getTopCustomersChart'])->name('top-customers-chart');
        Route::get('/stock-status-chart', [StatisticsController::class, 'getStockStatusChart'])->name('stock-status-chart');
        Route::get('/stock-movement-chart', [StatisticsController::class, 'getStockMovementChart'])->name('stock-movement-chart');
        Route::get('/discount-usage-chart', [StatisticsController::class, 'getDiscountUsageChart'])->name('discount-usage-chart');
        Route::get('/purchase-chart', [StatisticsController::class, 'getPurchaseChart'])->name('purchase-chart');
    });

    // AI & Insights (feature: ai_insights - Platinum only)
    Route::prefix('ai-insights')->name('ai-insights.')->middleware('feature.access:ai_insights')->group(function () {
        Route::get('/', [AiInsightController::class, 'index'])->name('index');
        Route::get('/{id}', [AiInsightController::class, 'show'])->name('show');
        Route::post('/{id}/read', [AiInsightController::class, 'markAsRead'])->name('mark-read');
        Route::post('/{id}/dismiss', [AiInsightController::class, 'dismiss'])->name('dismiss');
        Route::post('/mark-all-read', [AiInsightController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/calendar/summary', [AiInsightController::class, 'calendarSummary'])->name('calendar.summary');
        Route::get('/calendar/daily', [AiInsightController::class, 'daily'])->name('calendar.daily');
    });

    // Clara AI (feature: clara_ai - Platinum only)
    Route::prefix('clara-ai')->name('clara-ai.')->middleware('feature.access:clara_ai')->group(function () {
        Route::get('/', [ClaraAiController::class, 'index'])->name('index');
        Route::post('/chat', [ClaraAiController::class, 'chat'])->name('chat');
        Route::get('/new-session', [ClaraAiController::class, 'newSession'])->name('new-session');
        Route::delete('/session/{id}', [ClaraAiController::class, 'deleteSession'])->name('delete-session');
    });

    // FAQ (feature: help_faq)
    Route::prefix('faqs')->name('faqs.')->middleware('feature.access:help_faq')->group(function () {
        Route::get('/', [FaqController::class, 'index'])->name('index');
        Route::get('/{faq}', [FaqController::class, 'show'])->name('show');
        Route::post('/{faq}/helpful', [FaqController::class, 'markHelpful'])->name('helpful');
        Route::post('/{faq}/not-helpful', [FaqController::class, 'markNotHelpful'])->name('not-helpful');
    });

    // Landing Page Management (feature: landing_page - Platinum only)
    Route::prefix('landing-pages')->name('landing-pages.')->middleware('feature.access:landing_page')->group(function () {
        Route::get('/', [LandingPageController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [LandingPageController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LandingPageController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-status', [LandingPageController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Testimonials (feature: testimonials - Platinum only)
    Route::resource('testimonials', TestimonialController::class)->only(['index', 'destroy'])->middleware('feature.access:testimonials');
    Route::post('testimonials/{testimonial}/toggle-status', [TestimonialController::class, 'toggleStatus'])->name('testimonials.toggle-status')->middleware('feature.access:testimonials');

    // Reseller Applications (feature: reseller_app - Platinum only)
    Route::post('reseller-applications/toggle-acceptance', [ResellerApplicationController::class, 'toggleAcceptance'])->name('reseller-applications.toggle-acceptance')->middleware('feature.access:reseller_app');
    Route::resource('reseller-applications', ResellerApplicationController::class)->only(['index', 'store', 'update'])->middleware('feature.access:reseller_app');

});
