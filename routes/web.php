<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogInController;
use App\Http\Controllers\Admin\PathController;
use App\Http\Controllers\Admin\PathImageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TourController;
use App\Models\Path;
use App\Models\Room;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Routes accessible without authentication
*/

// Home page - displays main landing page
Route::get('/', [HomeController::class, 'index'])->name('index');

// AR Tour page - scan QR codes to view room information
Route::get('/scan-marker', [TourController::class, 'index'])->name('ar.view');

// Client-facing staff profile
Route::get('/staffs/{staff}', [StaffController::class, 'clientShow'])->name('staff.client-show');

Route::get('/api/rooms/{id}/exists', function ($id) {
    $exists = \App\Models\Room::where('id', $id)->exists();
    return response()->json(['exists' => $exists]);
});
/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
| Login and logout functionality for admin users
*/

// Admin login form (GET) and login processing (POST)
Route::get('/admin', [LogInController::class, 'showLoginForm'])->name('login');
Route::post('/admin', [LogInController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Authenticated Admin Routes
|--------------------------------------------------------------------------
| All routes below require admin authentication
*/

Route::middleware('auth')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Core Admin Routes
    |----------------------------------------------------------------------
    */

    // Logout - destroys admin session
    Route::post('/logout', [LogInController::class, 'logout'])->name('logout');

    // Admin dashboard - main landing page after login
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Admin profile management
    Route::get('/admin/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::post('/admin/profile/update-image', [ProfileController::class, 'updateImage'])->name('admin.profile.updateImage');

    /*
    |----------------------------------------------------------------------
    | Room Management Routes
    |----------------------------------------------------------------------
    | IMPORTANT: Specific routes MUST come before parameterized routes
    */

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

    /*
    |----------------------------------------------------------------------
    | Staff Management Routes
    |----------------------------------------------------------------------
    | Same principle: specific routes before parameterized routes
    */

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

    /*
    |----------------------------------------------------------------------
    | Path Management Routes
    |----------------------------------------------------------------------
    | Same principle: specific routes before parameterized routes
    */

    Route::prefix('admin')->name('path.')->group(function () {
        Route::get('/paths', [PathController::class, 'index'])->name('index');

        Route::get('/paths/create', [PathController::class, 'create'])->name('create');

        Route::post('/paths', [PathController::class, 'store'])->name('store');

        Route::delete('/paths/{path}', [PathController::class, 'destroy'])->name('destroy');
    });

    /*
    |----------------------------------------------------------------------
    | Path Images Management Routes
    |----------------------------------------------------------------------
    | Same principle: specific routes before parameterized routes
    */

    Route::prefix('admin/path-images')->name('path_images.')->group(function () {
        Route::get('/', [PathImageController::class, 'index'])->name('index');

        Route::get('/create', [PathImageController::class, 'create'])->name('create');

        Route::post('/', [PathImageController::class, 'store'])->name('store');

        Route::delete('/{pathImage}', [PathImageController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Route Order Rules
|--------------------------------------------------------------------------
| 
| 1. Static/Specific routes MUST come before dynamic/parameterized routes
| 2. More specific patterns should be defined before less specific ones
| 3. Routes are matched from top to bottom - first match wins
| 
| CORRECT ORDER:
| ✅ /rooms/create
| ✅ /rooms/recycle-bin  
| ✅ /rooms/{room}
| 
| INCORRECT ORDER:
| ❌ /rooms/{room}        <- This would catch everything
| ❌ /rooms/create        <- Never reached
| ❌ /rooms/recycle-bin   <- Never reached
|
*/