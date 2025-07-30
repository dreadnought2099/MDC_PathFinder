<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogInController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;


// Admin login (GET and POST)
Route::get('/admin', [LogInController::class, 'showLoginForm'])->name('login');
Route::post('/admin', [LogInController::class, 'login']);

// Authenticated admin routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::post('/admin/profile/update-image', [ProfileController::class, 'updateImage'])->name('admin.profile.updateImage');

});