<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterOutletController;
use App\Http\Controllers\OutletInformationController;
use App\Http\Controllers\ChangeOutletController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\RawMaterialAndSupplierController;
use App\Http\Controllers\PointOfSaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ProductHppController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

        Route::get('/sales-analytics', [ProductHppController::class, 'getSalesAnalytics'])
            ->name('sales-analytics');
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
});

Route::middleware(['auth'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PointOfSaleController::class, 'index'])->name('index');
    
    // Cart management
    Route::post('/cart/add', [PointOfSaleController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update', [PointOfSaleController::class, 'updateCartItem'])->name('cart.update');
    Route::delete('/cart/remove', [PointOfSaleController::class, 'removeCartItem'])->name('cart.remove');
    Route::post('/cart/clear', [PointOfSaleController::class, 'clearCart'])->name('cart.clear');
    
    // Discount & Customer
    Route::post('/discount/apply', [PointOfSaleController::class, 'applyDiscount'])->name('discount.apply');
    Route::post('/customer/set', [PointOfSaleController::class, 'setCustomer'])->name('customer.set');
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
});

Route::middleware(['auth'])->prefix('receipt')->name('receipt.')->group(function () {
    Route::get('print/{id}', [ReceiptController::class, 'printReceipt'])->name('print');
    Route::get('download/{id}', [ReceiptController::class, 'downloadReceipt'])->name('download');
    Route::get('preview/{id}', [ReceiptController::class, 'previewReceipt'])->name('preview');
});

require __DIR__.'/auth.php';