<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogInController;
use App\Http\Controllers\Admin\PathController;
use App\Http\Controllers\Admin\PathImageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScannerController;
use App\Models\Room;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    
    // Home page - displays main landing page
    Route::get('/', [HomeController::class, 'index'])->name('index');

    // Scanner page (no room yet)
    Route::get('/scan-marker', [ScannerController::class, 'index'])->name('scan.index');

    // Room details via token - SINGLE ROUTE with validation
    Route::get('/scan-marker/{room}', function (Room $room) {
        // Additional verification that the token format is correct
        if (!preg_match('/^[a-f0-9]{32}$/', $room->token)) {
            abort(404);
        }

        return app(ScannerController::class)->index($room);
    })->name('scan.room');

    // Client-facing staff profile
    Route::get('/staffs/{staff}', [StaffController::class, 'clientShow'])->name('staff.client-show');

    // API endpoint for checking room existence
    Route::get('/api/rooms/{token}/exists', function ($token) {
        $exists = \App\Models\Room::where('token', $token)->exists();
        return response()->json(['exists' => $exists]);
    });

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


Route::middleware('auth')->group(function () {

    Route::post('/logout', [LogInController::class, 'logout'])->name('logout');

    // Admin dashboard - main landing page after login
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Admin profile management
    Route::get('/admin/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::post('/admin/profile/update-image', [ProfileController::class, 'updateImage'])->name('admin.profile.updateImage');


    Route::prefix('admin')->name('room.')->group(function () {

        // List all rooms
        Route::get('/rooms', [RoomController::class, 'index'])->name('index');

        // SPECIFIC ROUTES - These MUST come before {room} routes
        Route::get('/rooms/create', [RoomController::class, 'create'])->name('create');
        Route::get('/rooms/recycle-bin', [RoomController::class, 'recycleBin'])->name('recycle-bin');

        // Staff assignment routes (also specific)
        Route::get('/rooms/assign/{roomId?}', [RoomController::class, 'assign'])->name('assign');
        Route::put('/rooms/assign', [RoomController::class, 'assignStaff'])->name('assign.update');

        // PARAMETERIZED ROUTES - These come after specific routes
        Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('show');
        Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::get('/rooms/{room}/print-qrcode', [RoomController::class, 'printQRCode'])->name('print-qrcode');

        // Form submission routes
        Route::post('/rooms', [RoomController::class, 'store'])->name('store');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('destroy');

        // Soft delete management (using IDs, not models)
        Route::post('/rooms/{id}/restore', [RoomController::class, 'restore'])->name('restore');
        Route::delete('/rooms/{id}/force-delete', [RoomController::class, 'forceDelete'])->name('forceDelete');

        // Media management
        Route::delete('/rooms/{room}/carousel/{image}', [RoomController::class, 'removeCarouselImage'])->name('carousel.remove');

        // Staff removal from room
        Route::delete('/rooms/staff/{id}/remove', [RoomController::class, 'removeFromRoom'])->name('staff.remove');
    });

    Route::prefix('admin')->name('staff.')->group(function () {

        // List all staff
        Route::get('/staff', [StaffController::class, 'index'])->name('index');

        // SPECIFIC ROUTES - These MUST come before {staff} routes
        Route::get('/staff/create', [StaffController::class, 'create'])->name('create');
        Route::get('/staff/recycle-bin', [RoomController::class, 'recycleBin'])->name('recycle-bin');

        // PARAMETERIZED ROUTES - These come after specific routes
        Route::get('/staff/{staff}', [StaffController::class, 'show'])->name('show');
        Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('edit');

        // Form submission routes
        Route::post('/staff', [StaffController::class, 'store'])->name('store');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('update');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('destroy');

        // Soft delete management (using IDs, not models)
        Route::post('/staff/{id}/restore', [StaffController::class, 'restore'])->name('restore');
        Route::delete('/staff/{id}/force-delete', [StaffController::class, 'forceDelete'])->name('forceDelete');
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
        Route::post('/path-images', [PathImageController::class, 'store'])->name('store');

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
