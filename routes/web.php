<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogInController;
use App\Http\Controllers\Admin\PathController;
use App\Http\Controllers\Admin\PathImageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RecycleBinController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomUserController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {

    //  To test custom error page
    Route::get('/test-error/{code}', function ($code) {
        // List of allowed codes for safety
        $allowedCodes = [401, 402, 403, 404, 419, 429, 500, 503];

        if (!in_array($code, $allowedCodes)) {
            abort(400, 'Invalid test error code.');
        }

        abort($code);
    });

    // Home page - displays main landing page
    Route::get('/', [HomeController::class, 'index'])->name('index');

    Route::get('/about', function () {
        return view('pages.client.about.index');
    })->name('about');

    Route::get('/meet-the-team', function () {
        return view('pages.client.team.index');
    })->name('pages.team');

    // Scanner page (no room yet)
    Route::get('/scan-marker', [ScannerController::class, 'index'])->name('scan.index');

    // Room details via token - now using dedicated controller method
    Route::get('/scan-marker/{token}', [TokenController::class, 'getRoomByToken'])
        ->name('scan.room')
        ->where('token', '[a-f0-9]{32}'); // Route constraint for performance

    //  Check if a room exists by token (used by QR scanner JS)
    Route::get('/rooms/{token}/exists', [TokenController::class, 'checkRoomExists'])
        ->name('rooms.exists')
        ->where('token', '[a-f0-9]{32}');

    // Client-facing staff profile
    Route::get('/staffs/{staffToken}', [StaffController::class, 'clientShow'])->name('staff.client-show');

    // Client-side navigation
    Route::get('/navigation/select', [PathController::class, 'selection'])->name('paths.select');
    Route::post('/navigation/results', [PathController::class, 'results'])->name('paths.results');

    // Return to results
    Route::get('/paths/return-to-results', [PathController::class, 'returnToResults'])
        ->name('paths.return-to-results');
});

// Admin login form (GET) and login processing (POST)
Route::get('/admin', [LogInController::class, 'showLoginForm'])->name('login');
Route::post('/admin', [LogInController::class, 'login']);

// Routes accessible after login, before 2FA verification
Route::middleware(['auth', 'role:Admin|Room Manager'])->group(function () {
    // Verify 2FA OTP
    Route::post('/admin/profile/2fa/verify', [TwoFactorController::class, 'verifyOTP'])
        ->name('admin.2fa.verifyOTP');

    // Recovery codes verification
    Route::post('/admin/2fa/recovery', [TwoFactorController::class, 'verifyRecoveryCode'])
        ->name('admin.2fa.recovery.verify');
});

Route::middleware('auth', 'role:Admin|Room Manager', '2fa')->group(function () {
    Route::post('/logout', [LogInController::class, 'logout'])->name('logout');

    // 2FA Management
    Route::prefix('admin/profile/2fa')->group(function () {
        Route::post('/enable', [TwoFactorController::class, 'enable'])
            ->name('admin.profile.2fa.enable');

        Route::post('/disable', [TwoFactorController::class, 'disable'])
            ->name('admin.profile.2fa.disable');

        Route::post('/regenerate', [TwoFactorController::class, 'regenerate'])
            ->name('admin.profile.2fa.regenerate');

        Route::post('/recovery/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes'])
            ->name('admin.profile.2fa.recovery.regenerate');
    });

    // Recovery code download
    Route::get('/admin/2fa/recovery-codes/download', [TwoFactorController::class, 'downloadRecoveryCodes'])
        ->name('admin.2fa.recovery.download');

    // Admin dashboard - main landing page after login
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Admin profile management
    Route::get('/admin/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::post('/admin/profile/update-image', [ProfileController::class, 'updateImage'])->name('admin.profile.updateImage');
    Route::get('/admin/recycle-bin', [RecycleBinController::class, 'index'])->middleware('permission: delete rooms|delete staff|delete room users')->name('recycle-bin');

    Route::prefix('admin')->name('room.')->group(function () {
        // Check office name to prevent duplicates to be inserted
        Route::get('/rooms/check-name', [RoomController::class, 'checkName'])->name('check-name');

        // List all rooms
        Route::get('/rooms', [RoomController::class, 'index'])->middleware('permission:view rooms')->name('index');

        // SPECIFIC ROUTES - These MUST come before {room} routes
        Route::get('/rooms/create', [RoomController::class, 'create'])->middleware('permission:create rooms')->name('create');

        // Staff assignment routes (also specific)
        Route::get('/rooms/assign/{roomId?}', [RoomController::class, 'assign'])->middleware('permission:edit staff')->name('assign');
        Route::put('/rooms/assign', [RoomController::class, 'assignStaff'])->middleware('permission:edit staff')->name('assign.update');

        // PARAMETERIZED ROUTES - These come after specific routes
        Route::get('/rooms/{room}', [RoomController::class, 'show'])->middleware('permission:view rooms')->name('show');
        Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->middleware('permission:edit rooms')->name('edit');
        Route::get('/rooms/{room}/print-qrcode', [RoomController::class, 'printQRCode'])->middleware('permission:view rooms')->name('print-qrcode');

        // Form submission routes
        Route::post('/rooms', [RoomController::class, 'store'])->middleware('permission:create rooms')->name('store');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->middleware('permission:edit rooms')->name('update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->middleware('permission:delete rooms')->name('destroy');

        // Soft delete management (using IDs, not models)
        Route::post('/rooms/{id}/restore', [RoomController::class, 'restore'])->middleware('permission:delete rooms')->name('restore');
        Route::delete('/rooms/{id}/force-delete', [RoomController::class, 'forceDelete'])->middleware('permission:delete rooms')->name('forceDelete');

        // Media management
        Route::delete('/rooms/{room}/carousel/{image}', [RoomController::class, 'removeCarouselImage'])->middleware('permission:edit rooms')->name('carousel.remove');

        // Staff removal from room
        Route::delete('/rooms/staff/{id}/remove', [RoomController::class, 'removeFromRoom'])->middleware('permission:edit staff')->name('staff.remove');
    });

    Route::prefix('admin')->name('room-user.')->group(function () {

        Route::get('/room-users', [RoomUserController::class, 'index'])->middleware('permission:view room users')->name('index');
        Route::get('/room-users/create', [RoomUserController::class, 'create'])->middleware('permission:create room users')->name('create');

        Route::post('/room-users', [RoomUserController::class, 'store'])->middleware('permission:create room users')->name('store');
        Route::get('/room-users/{user}', [RoomUserController::class, 'show'])->middleware('permission:view room users')->name('show');

        Route::get('/room-users/{user}/edit', [RoomUserController::class, 'edit'])->middleware('permission:edit room users')->name('edit');
        Route::put('/room-users/{user}', [RoomUserController::class, 'update'])->middleware('permission:edit room users')->name('update');
        Route::delete('/room-users/{user}', [RoomUserController::class, 'destroy'])->middleware('permission:delete room users')->name('destroy');

        Route::post('/room-users/{id}/restore', [RoomUserController::class, 'restore'])->middleware('permission:delete room users')->name('restore');
        Route::delete('/room-users/{id}/force-delete', [RoomUserController::class, 'forceDelete'])->middleware('permission:delete room users')->name('forceDelete');

        Route::patch('/room-users/{user}/toggle-status', [RoomUserController::class, 'toggleStatus'])->middleware('role:Admin')->name('toggle-status');
    });

    Route::prefix('admin')->name('staff.')->group(function () {

        // List all staff
        Route::get('/staff', [StaffController::class, 'index'])->middleware('permission:view staff')->name('index');

        // SPECIFIC ROUTES - These MUST come before {staff} routes
        Route::get('/staff/create', [StaffController::class, 'create'])->middleware('permission:create staff')->name('create');

        // PARAMETERIZED ROUTES - These come after specific routes
        Route::get('/staff/{staff}', [StaffController::class, 'show'])->middleware('permission:view staff')->name('show');
        Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->middleware('permission:edit staff')->name('edit');

        // Form submission routes
        Route::post('/staff', [StaffController::class, 'store'])->middleware('permission:create staff')->name('store');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->middleware('permission:edit staff')->name('update');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->middleware('permission:delete staff')->name('destroy');

        // Soft delete management (using IDs, not models)
        Route::post('/staff/{id}/restore', [StaffController::class, 'restore'])->middleware('permission:delete staff')->name('restore');
        Route::delete('/staff/{id}/force-delete', [StaffController::class, 'forceDelete'])->middleware('permission:delete staff')->name('forceDelete');
    });


    Route::prefix('admin')->name('path.')->group(function () {

        // List all paths
        Route::get('/paths', [PathController::class, 'index'])->name('index');

        // PARAMETERIZED ROUTES - These come after specific routes
        Route::get('/paths/{path}', [PathController::class, 'show'])->name('show');
    });

    Route::prefix('admin')->name('path-image.')->group(function () {
        // GENERIC ROUTES (no path parameter needed)
        Route::get('/path-images/create/{path?}', [PathImageController::class, 'create'])->name('create');
        Route::post('/path-images/create', [PathImageController::class, 'store'])->name('store');

        // UNIFIED EDIT ROUTE (handles both single and multiple images)
        Route::get('/paths/{path}/images/edit/{pathImage?}', [PathImageController::class, 'edit'])->name('edit');

        // PATH-SPECIFIC ROUTES (operate on collections of images under a path)
        Route::put('/paths/{path}/images/order', [PathImageController::class, 'updateOrder'])->name('update-order');
        Route::patch('/paths/{path}/images', [PathImageController::class, 'updateMultiple'])->name('update-multiple');
        Route::delete('/paths/{path}/images/bulk', [PathImageController::class, 'destroyMultiple'])->name('destroy-multiple');

        // INDIVIDUAL IMAGE ROUTES
        Route::put('/path-images/{pathImage}', [PathImageController::class, 'updateSingle'])->name('update-single');
        Route::delete('/path-images/{pathImage}', [PathImageController::class, 'destroySingle'])->name('destroy-single');
    });
});
