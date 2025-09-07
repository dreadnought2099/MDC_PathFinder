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


// routes/api.php
Route::get('/rooms/{room}/exists', function ($roomToken) {
    try {
        // Check if room exists by token
        $room = \App\Models\Room::where('token', $roomToken)->first();

        return response()->json([
            'exists' => $room !== null,
            'room_id' => $room ? $room->id : null
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'exists' => false,
            'error' => 'Room check failed'
        ], 500);
    }
});