<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogInController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('index');

Route::get('/scan-marker', [TourController::class, 'index'])->name('ar.view');

// Admin login (GET and POST)
Route::get('/admin', [LogInController::class, 'showLoginForm'])->name('login');
Route::post('/admin', [LogInController::class, 'login']);

// Authenticated admin routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogInController::class, 'logout'])->name('logout');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::post('/admin/profile/update-image', [ProfileController::class, 'updateImage'])->name('admin.profile.updateImage');


    Route::prefix('admin')->name('room.')->group(function () {
        Route::get('/room', [RoomController::class, 'index'])->name('index');
        Route::get('/room/create', [RoomController::class, 'create'])->name('create');
        Route::post('/room', [RoomController::class, 'store'])->name('store');
        Route::get('/room/{room}', [RoomController::class, 'show'])->name('show');
        Route::get('/room/{room}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::put('/room/{room}', [RoomController::class, 'update'])->name('update');
        Route::delete('/room/{room}', [RoomController::class, 'destroy'])->name('destroy');
        Route::get('/rooms/recycle-bin', [RoomController::class, 'recycleBin'])->name('recycle-bin');
        Route::post('/rooms/{id}/restore', [RoomController::class, 'restore'])->name('restore');
        Route::delete('/rooms/{id}/force-delete', [RoomController::class, 'forceDelete'])->name('forceDelete');
        Route::delete('/rooms/{room}/carousel/{image}', [RoomController::class, 'removeCarouselImage'])->name('carousel.remove');
    });


    Route::prefix('admin')->name('staff.')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('create');
        Route::post('/staff', [StaffController::class, 'store'])->name('store');
        Route::get('/staff/{staff}', [StaffController::class, 'show'])->name('show');
        Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('update');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('destroy');
        Route::get('/staffs/recycle-bin', [RoomController::class, 'recycleBin'])->name('recycle-bin');
        Route::post('/staffs/{id}/restore', [StaffController::class, 'restore'])->name('restore');
        Route::delete('/staffs/{id}/force-delete', [StaffController::class, 'forceDelete'])->name('forceDelete');
    });
});
