<?php

use App\Http\Controllers\Admin\Auth\LogInController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Auth\TwoFactorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Path\PathController;
use App\Http\Controllers\Admin\Path\PathImageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RecycleBinController;
use App\Http\Controllers\Admin\Room\RoomAssignmentController;
use App\Http\Controllers\Admin\Room\RoomController;
use App\Http\Controllers\Admin\Room\RoomUserController;
use App\Http\Controllers\Admin\Staff\StaffController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;


// Public pages
Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/about', fn() => view('pages.client.about.index'))->name('about');
Route::get('/meet-the-team', fn() => view('pages.client.team.index'))->name('pages.team');

// Scanner & token-based room routes
Route::get('/scan-marker', [ScannerController::class, 'index'])->name('scan.index');
Route::get('/scan-marker/{token}', [TokenController::class, 'getRoomByToken'])
    ->name('scan.room')->where('token', '[a-f0-9]{64}');
Route::get('/rooms/{token}/exists', [TokenController::class, 'checkRoomExists'])
    ->name('rooms.exists')->where('token', '[a-f0-9]{64}');

// Client-facing staff
Route::get('/staff/search', [StaffController::class, 'search'])->name('search');
Route::get('/staff/{staffToken}', [StaffController::class, 'clientShow'])->name('staff.client-show');

// Client-side navigation
Route::get('/navigation/select', [PathController::class, 'selection'])->name('paths.select');
Route::post('/navigation/results', [PathController::class, 'results'])->name('paths.results');
Route::get('/navigation/return-to-results', [PathController::class, 'returnToResults'])->name('paths.return-to-results');

// Admin login
Route::get('/admin', [LogInController::class, 'showLoginForm'])->name('login');
Route::post('/admin', [LogInController::class, 'login']);

// Admin password reset
Route::get('/admin/forgot-password', [ResetPasswordController::class, 'showRequestForm'])->name('password.request');
Route::post('/admin/forgot-password', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/admin/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/admin/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');

// Routes available after login, before 2FA
Route::middleware(['auth', 'role:Admin|Office Manager'])->group(function () {

    Route::get('/admin/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])
        ->name('admin.2fa.showVerifyForm');
    Route::post('/admin/2fa/verify', [TwoFactorController::class, 'verifyOTP'])
        ->name('admin.2fa.verifyOTP');
    Route::post('/admin/2fa/recovery', [TwoFactorController::class, 'verifyRecoveryCode'])
        ->name('admin.2fa.recovery.verify');
});

// Routes protected by auth + role + 2FA
Route::middleware(['auth', 'role:Admin|Office Manager', '2fa'])->prefix('admin')->group(function () {

    // Logout
    Route::post('/logout', [LogInController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Profile & 2FA
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('admin.profile');
        Route::post('/update-image', [ProfileController::class, 'updateImage'])->name('admin.profile.updateImage');

        Route::prefix('2fa')->group(function () {
            Route::post('/enable', [TwoFactorController::class, 'enable'])->name('admin.profile.2fa.enable');
            Route::post('/disable', [TwoFactorController::class, 'disable'])->name('admin.profile.2fa.disable');
            Route::post('/regenerate', [TwoFactorController::class, 'regenerate'])->name('admin.profile.2fa.regenerate');
            Route::post('/recovery/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes'])
                ->name('admin.profile.2fa.recovery.regenerate');
            Route::get('/recovery-codes/download', [TwoFactorController::class, 'downloadRecoveryCodes'])
                ->name('admin.2fa.recovery.download');
        });
    });

    // Recycle bin
    Route::get('/recycle-bin', [RecycleBinController::class, 'index'])
        ->middleware('permission: delete rooms|delete staff|delete room users')->name('recycle-bin');

    // Rooms
    Route::prefix('rooms')->name('room.')->group(function () {
        Route::get('/check-name', [RoomController::class, 'checkName'])->name('check-name');
        Route::get('/', [RoomController::class, 'index'])->middleware('permission:view rooms')->name('index');
        Route::get('/create', [RoomController::class, 'create'])->middleware('permission:create rooms')->name('create');
        Route::get('/assign/{roomId?}', [RoomAssignmentController::class, 'assign'])->middleware('permission:edit staff')->name('assign');
        Route::put('/assign', [RoomAssignmentController::class, 'assignStaff'])->middleware('permission:edit staff')->name('assign.update');

        Route::get('/{room}', [RoomController::class, 'show'])->middleware('permission:view rooms')->name('show');
        Route::get('/{room}/edit', [RoomController::class, 'edit'])->middleware('permission:edit rooms')->name('edit');
        Route::get('/{room}/print-qrcode', [RoomController::class, 'printQRCode'])->middleware('permission:view rooms')->name('print-qrcode');

        Route::post('/', [RoomController::class, 'store'])->middleware('permission:create rooms')->name('store');
        Route::put('/{room}', [RoomController::class, 'update'])->middleware('permission:edit rooms')->name('update');
        Route::delete('/{room}', [RoomController::class, 'destroy'])->middleware('permission:delete rooms')->name('destroy');

        Route::post('/{id}/restore', [RoomController::class, 'restore'])->middleware('permission:delete rooms')->name('restore');
        Route::delete('/{id}/force-delete', [RoomController::class, 'forceDelete'])->middleware('permission:delete rooms')->name('forceDelete');

        Route::delete('/{room}/carousel/{image}', [RoomController::class, 'removeCarouselImage'])
            ->middleware('permission:edit rooms')->name('carousel.remove');
        Route::delete('/staff/{id}/remove', [RoomAssignmentController::class, 'removeFromRoom'])
            ->middleware('permission:edit staff')->name('staff.remove');
    });

    // Room Users
    Route::prefix('room-users')->name('room-user.')->group(function () {
        Route::get('/', [RoomUserController::class, 'index'])->middleware('permission:view room users')->name('index');
        Route::get('/create', [RoomUserController::class, 'create'])->middleware('permission:create room users')->name('create');
        Route::post('/', [RoomUserController::class, 'store'])->middleware('permission:create room users')->name('store');
        Route::get('/{user}', [RoomUserController::class, 'show'])->middleware('permission:view room users')->name('show');
        Route::get('/{user}/edit', [RoomUserController::class, 'edit'])->middleware('permission:edit room users')->name('edit');
        Route::put('/{user}', [RoomUserController::class, 'update'])->middleware('permission:edit room users')->name('update');
        Route::delete('/{user}', [RoomUserController::class, 'destroy'])->middleware('permission:delete room users')->name('destroy');
        Route::post('/{id}/restore', [RoomUserController::class, 'restore'])->middleware('permission:delete room users')->name('restore');
        Route::delete('/{id}/force-delete', [RoomUserController::class, 'forceDelete'])->middleware('permission:delete room users')->name('forceDelete');
        Route::patch('/{user}/toggle-status', [RoomUserController::class, 'toggleStatus'])->middleware('role:Admin')->name('toggle-status');
    });

    // Staff
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/check-email', [StaffController::class, 'checkEmail'])->name('checkEmail');
        Route::get('/', [StaffController::class, 'index'])->middleware('permission:view staff')->name('index');
        Route::get('/create', [StaffController::class, 'create'])->middleware('permission:create staff')->name('create');
        Route::get('/{staff}', [StaffController::class, 'show'])->middleware('permission:view staff')->name('show');
        Route::get('/{staff}/edit', [StaffController::class, 'edit'])->middleware('permission:edit staff')->name('edit');
        Route::post('/', [StaffController::class, 'store'])->middleware('permission:create staff')->name('store');
        Route::put('/{staff}', [StaffController::class, 'update'])->middleware('permission:edit staff')->name('update');
        Route::delete('/{staff}', [StaffController::class, 'destroy'])->middleware('permission:delete staff')->name('destroy');
        Route::post('/{id}/restore', [StaffController::class, 'restore'])->middleware('permission:delete staff')->name('restore');
        Route::delete('/{id}/force-delete', [StaffController::class, 'forceDelete'])->middleware('permission:delete staff')->name('forceDelete');
    });

    // Paths & Path Images
    Route::prefix('paths')->name('path.')->group(function () {
        Route::get('/', [PathController::class, 'index'])->name('index');
        Route::get('/{path}', [PathController::class, 'show'])->name('show');
    });

    Route::prefix('path-images')->name('path-image.')->group(function () {
        Route::get('/create/{path?}', [PathImageController::class, 'create'])->name('create');
        Route::post('/', [PathImageController::class, 'store'])->name('store');

        // Path Images
        Route::get('/{path}/images/edit/{pathImage?}', [PathImageController::class, 'edit'])->name('edit');
        Route::put('/{path}/images/order', [PathImageController::class, 'updateOrder'])->name('update-order');
        Route::patch('/{path}/images', [PathImageController::class, 'updateMultiple'])->name('update-multiple');
        Route::delete('/{path}/images/bulk', [PathImageController::class, 'destroyMultiple'])->name('destroy-multiple');

        Route::put('/{pathImage}', [PathImageController::class, 'updateSingle'])->name('update-single');
        Route::delete('/{pathImage}', [PathImageController::class, 'destroySingle'])->name('destroy-single');
    });
});
