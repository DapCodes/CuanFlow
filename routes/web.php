<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterOutletController;
use App\Http\Controllers\OutletInformationController;
use App\Http\Controllers\ChangeOutletController;
use App\Http\Controllers\RawMaterialAndSupplierController;

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
    });
        
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';