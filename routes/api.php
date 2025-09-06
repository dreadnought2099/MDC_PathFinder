<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PathController;

Route::prefix('api')->name('api.')->group(function () {
    // Rooms
    Route::get('/rooms', [PathController::class, 'getRooms'])->name('rooms');

    // Paths
    Route::get('/popular-paths', [PathController::class, 'getPopularPaths'])->name('paths.popular');
    Route::get('/navigation/route', [PathController::class, 'getNavigationRoute'])->name('navigation.route');
    Route::get('/navigation/path/{id}', [PathController::class, 'getPathNavigation'])->name('navigation.path');
    Route::get('/navigation/paths', [PathController::class, 'getNavigationPaths'])->name('navigation.paths');
});
