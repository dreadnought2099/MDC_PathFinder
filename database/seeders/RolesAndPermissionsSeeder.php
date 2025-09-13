<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions
        Permission::firstOrCreate(['name' => 'manage rooms']);
        Permission::firstOrCreate(['name' => 'manage staff']);

        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Room Manager']);

        // Assign permissions
        $adminRole->givePermissionTo(Permission::all());
        $managerRole->givePermissionTo(['manage rooms', 'manage staff']);

        // Default Admin
        $admin = User::firstOrCreate(
            ['email' => 'mdcpathfinder@gmail.com'], // required for admin
            [
                'name' => 'Administrator',           // optional but nice to have
                'username' => 'Admin',                  // admin doesn't need username
                'password' => Hash::make('iamp@thfinderadmin'),
            ]
        );
        $admin->assignRole($adminRole);
    }
}
