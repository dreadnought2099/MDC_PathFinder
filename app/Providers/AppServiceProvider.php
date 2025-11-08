<?php

namespace App\Providers;

use App\Services\EntrancePointService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Room;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(EntrancePointService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View Composer for create/edit room views
        View::composer(['pages.admin.rooms.create', 'pages.admin.rooms.edit'], function ($view) {
            $view->with('days', Room::validDays());
        });
    }
}
