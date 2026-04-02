<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminLandingPageController;
use App\Http\Controllers\Admin\AdminLandingSectionController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\AdminWithdrawController;
use App\Http\Controllers\Admin\BannedIpController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\LoginHistoryController;
use App\Http\Controllers\Admin\OutletController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PermissionCategoryController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SubscriptionFeatureController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\SubscriptionSettingController;
use App\Http\Controllers\Admin\SubscriptionTierController;
use App\Http\Controllers\Admin\TaskLabelController;
use App\Http\Controllers\Admin\TaskStatusController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TrialVerificationController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CpuMonitoringController;
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
    Route::get('/dashboard/active-users', [DashboardController::class, 'activeUsersCount'])->name('admin.dashboard.active-users');
    Route::get('/dashboard/active-users-list', [DashboardController::class, 'activeUsersList'])->name('admin.dashboard.active-users-list');
    Route::get('/', fn () => redirect()->route('admin.dashboard'));

    // Outlets
    Route::resource('outlets', OutletController::class)->only(['index', 'show'])->names('admin.outlets');
    Route::post('outlets/{outlet}/toggle-status', [OutletController::class, 'toggleStatus'])->name('admin.outlets.toggle-status');

    // Data Master
    Route::resource('roles', RoleController::class)->names('admin.roles');
    Route::resource('advertisements', \App\Http\Controllers\Admin\AdvertisementController::class)->names('admin.advertisements');
    Route::post('advertisements/{advertisement}/toggle-status', [\App\Http\Controllers\Admin\AdvertisementController::class, 'toggleStatus'])->name('admin.advertisements.toggle-status');
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

    // Admin Landing Pages Management
    Route::prefix('landing-pages')->name('admin.landing-pages.')->group(function () {
        Route::get('/', [AdminLandingPageController::class, 'index'])->name('index');
        Route::get('/create', [AdminLandingPageController::class, 'create'])->name('create');
        Route::post('/', [AdminLandingPageController::class, 'store'])->name('store');
        Route::get('/{landingPage}', [AdminLandingPageController::class, 'show'])->name('show');
        Route::get('/{landingPage}/edit', [AdminLandingPageController::class, 'edit'])->name('edit');
        Route::put('/{landingPage}', [AdminLandingPageController::class, 'update'])->name('update');
        Route::delete('/{landingPage}', [AdminLandingPageController::class, 'destroy'])->name('destroy');
        Route::post('/{landingPage}/toggle-status', [AdminLandingPageController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{landingPage}/preview', [AdminLandingPageController::class, 'preview'])->name('preview');
        Route::put('/{landingPage}/cta', [AdminLandingPageController::class, 'updateCta'])->name('update-cta');

        // Section Management
        Route::get('/{landingPage}/sections', [AdminLandingSectionController::class, 'index'])->name('sections.index');
        Route::get('/{landingPage}/sections/{section}/edit', [AdminLandingSectionController::class, 'edit'])->name('sections.edit');
        Route::put('/{landingPage}/sections/{section}', [AdminLandingSectionController::class, 'update'])->name('sections.update');
        Route::post('/{landingPage}/sections/{section}/toggle', [AdminLandingSectionController::class, 'toggleStatus'])->name('sections.toggle');
        Route::post('/{landingPage}/sections/reorder', [AdminLandingSectionController::class, 'reorder'])->name('sections.reorder');

        // Section Items
        Route::post('/{landingPage}/sections/{section}/items', [AdminLandingSectionController::class, 'storeItem'])->name('sections.items.store');
        Route::put('/{landingPage}/sections/{section}/items/{item}', [AdminLandingSectionController::class, 'updateItem'])->name('sections.items.update');
        Route::delete('/{landingPage}/sections/{section}/items/{item}', [AdminLandingSectionController::class, 'destroyItem'])->name('sections.items.destroy');
        Route::post('/{landingPage}/sections/{section}/items/reorder', [AdminLandingSectionController::class, 'reorderItems'])->name('sections.items.reorder');
    });

    // Subscription Management
    Route::prefix('subscription')->name('admin.subscription-')->group(function () {
        Route::resource('tiers', SubscriptionTierController::class)->names('tiers');
        Route::resource('plans', SubscriptionPlanController::class)->names('plans');
        Route::resource('features', SubscriptionFeatureController::class)->names('features');

        // Settings
        Route::get('settings', [SubscriptionSettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SubscriptionSettingController::class, 'update'])->name('settings.update');

        // Trial Requests
        Route::get('trial-requests', [TrialVerificationController::class, 'index'])->name('trial-requests.index');
        Route::get('trial-requests/{trialRequest}', [TrialVerificationController::class, 'show'])->name('trial-requests.show');
        Route::post('trial-requests/{trialRequest}/approve', [TrialVerificationController::class, 'approve'])->name('trial-requests.approve');
        Route::post('trial-requests/{trialRequest}/reject', [TrialVerificationController::class, 'reject'])->name('trial-requests.reject');

        // User Subscriptions
        Route::get('users', [App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('users.index');
        Route::get('users/{subscription}', [App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('users.show');
        Route::post('users/{subscription}/status', [App\Http\Controllers\Admin\SubscriptionController::class, 'updateStatus'])->name('users.status');

        // Payment History
        Route::get('payments', [App\Http\Controllers\Admin\SubscriptionPaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [App\Http\Controllers\Admin\SubscriptionPaymentController::class, 'show'])->name('payments.show');
        Route::post('payments/{payment}/approve', [App\Http\Controllers\Admin\SubscriptionPaymentController::class, 'approve'])->name('payments.approve');
    });

    // Security - IP Ban & Login Tracking
    Route::prefix('security')->name('admin.security.')->group(function () {
        Route::get('login-histories', [LoginHistoryController::class, 'index'])->name('login-histories.index');
        Route::post('banned-ips', [BannedIpController::class, 'store'])->name('banned-ips.store');
        Route::get('banned-ips', [BannedIpController::class, 'index'])->name('banned-ips.index');
        Route::delete('banned-ips/{bannedIp}', [BannedIpController::class, 'destroy'])->name('banned-ips.destroy');

        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::post('/backup', [ActivityLogController::class, 'backup'])->name('backup');
            Route::get('/archives', [ActivityLogController::class, 'archives'])->name('archives');
            Route::get('/archives/{id}/view', [ActivityLogController::class, 'viewArchive'])->name('archives.view');
            Route::get('/archives/{id}/download', [ActivityLogController::class, 'downloadArchive'])->name('archives.download');
            Route::get('/{id}', [ActivityLogController::class, 'show'])->name('show');
        });

        Route::prefix('error-logs')->name('error-logs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SystemErrorLogController::class, 'index'])->name('index');
            Route::post('/clear', [\App\Http\Controllers\Admin\SystemErrorLogController::class, 'clear'])->name('clear');
        });
    });

    // Terms & Conditions Management
    Route::get('/terms', [App\Http\Controllers\Admin\TermAndConditionController::class, 'edit'])->name('admin.terms.edit');
    Route::put('/terms', [App\Http\Controllers\Admin\TermAndConditionController::class, 'update'])->name('admin.terms.update');

    // Maintenance Management
    Route::prefix('maintenance')->name('admin.maintenance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MaintenanceController::class, 'index'])->name('index');
        Route::post('/toggle', [\App\Http\Controllers\Admin\MaintenanceController::class, 'toggle'])->name('toggle');
        Route::get('/history', [\App\Http\Controllers\Admin\MaintenanceController::class, 'history'])->name('history');
        Route::get('/broadcast', [\App\Http\Controllers\Admin\MaintenanceController::class, 'broadcast'])->name('broadcast');
        Route::post('/broadcast', [\App\Http\Controllers\Admin\MaintenanceController::class, 'sendBroadcast'])->name('broadcast.send');
        Route::delete('/session/{sessionId}', [\App\Http\Controllers\Admin\MaintenanceController::class, 'terminateSession'])->name('session.terminate');
    });

    // CPU Monitoring
    Route::get('/cpu-monitoring', [CpuMonitoringController::class, 'index'])->name('admin.cpu-monitoring.index');
    Route::get('/api/cpu-usage', [CpuMonitoringController::class, 'getUsage'])->name('admin.cpu-monitoring.api');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
});
