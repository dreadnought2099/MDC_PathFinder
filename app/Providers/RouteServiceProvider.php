<?php

namespace App\Providers;

use App\Models\Staff;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Custom binding for client token
        Route::bind('staffToken', function ($value) {
            return Staff::with('room')
                ->where('token', $value)
                ->firstOrFail();
        });
    }
}
