<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\OutletController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminWithdrawController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\PermissionCategoryController;
use App\Http\Controllers\Admin\TaskStatusController;
use App\Http\Controllers\Admin\TaskLabelController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\AdminManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
});

// Authenticated admin routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    
    // Outlets
    Route::resource('outlets', OutletController::class)->only(['index', 'show'])->names('admin.outlets');
    Route::post('outlets/{outlet}/toggle-status', [OutletController::class, 'toggleStatus'])->name('admin.outlets.toggle-status');
    
    // Data Master
    Route::resource('roles', RoleController::class)->names('admin.roles');
    Route::resource('permissions', PermissionController::class)->names('admin.permissions');
    Route::resource('permission-categories', PermissionCategoryController::class)->names('admin.permission-categories');
    Route::resource('task-statuses', TaskStatusController::class)->names('admin.task-statuses');
    Route::resource('task-labels', TaskLabelController::class)->names('admin.task-labels');
    Route::resource('testimonials', TestimonialController::class)->names('admin.testimonials');
    Route::post('testimonials/{testimonial}/toggle-status', [TestimonialController::class, 'toggleStatus'])->name('admin.testimonials.toggle-status');
    Route::resource('users', UserController::class)->names('admin.users');
    Route::resource('admins', AdminManagementController::class)->names('admin.admins');
    Route::resource('units', UnitController::class)->names('admin.units');
    Route::resource('expense-categories', ExpenseCategoryController::class)->names('admin.expense-categories');
    Route::resource('faqs', FaqController::class)->names('admin.faqs');
    Route::post('faqs/{faq}/toggle-status', [FaqController::class, 'toggleStatus'])->name('admin.faqs.toggle-status');
    
    // Categories
    Route::resource('categories', CategoryController::class)->names('admin.categories');
    Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle-status');
    
    // Payment Methods
    Route::resource('payment-methods', PaymentMethodController::class)->names('admin.payment-methods');
    Route::post('payment-methods/{paymentMethod}/toggle-status', [PaymentMethodController::class, 'toggleStatus'])->name('admin.payment-methods.toggle-status');

    // Withdrawals Management
    Route::prefix('withdrawals')->name('admin.withdrawals.')->group(function () {
        Route::get('/', [AdminWithdrawController::class, 'index'])->name('index');
        Route::get('/settings', [AdminWithdrawController::class, 'taxSettings'])->name('tax-settings');
        Route::post('/settings', [AdminWithdrawController::class, 'updateTaxSettings'])->name('tax-settings.update');
        Route::get('/{withdrawal}', [AdminWithdrawController::class, 'show'])->name('show');
        Route::post('/{withdrawal}/approve', [AdminWithdrawController::class, 'approve'])->name('approve');
        Route::post('/{withdrawal}/reject', [AdminWithdrawController::class, 'reject'])->name('reject');
        Route::post('/{withdrawal}/approve-by-owner', [AdminWithdrawController::class, 'approveByOwner'])->name('approve-by-owner');
        Route::post('/{withdrawal}/reject-by-owner', [AdminWithdrawController::class, 'rejectByOwner'])->name('reject-by-owner');
        Route::post('/{withdrawal}/paid', [AdminWithdrawController::class, 'markAsPaid'])->name('paid');
    });
});
