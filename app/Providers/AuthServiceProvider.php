<?php

namespace App\Providers;

use App\Models\Room;
use App\Models\Staff;
use App\Models\User;
use App\Policies\RoomPolicy;
use App\Policies\StaffPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Room::class => RoomPolicy::class,
        Staff::class => StaffPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Can define additional gates here if needed
    }
}
