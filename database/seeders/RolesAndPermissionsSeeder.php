<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
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
        // Permissions for Office
        Permission::firstOrCreate(['name' => 'view rooms']);
        Permission::firstOrCreate(['name' => 'create rooms']);
        Permission::firstOrCreate(['name' => 'edit rooms']);
        Permission::firstOrCreate(['name' => 'delete rooms']);

        // Permission for Staff
        Permission::firstOrCreate(['name' => 'view staff']);
        Permission::firstOrCreate(['name' => 'create staff']);
        Permission::firstOrCreate(['name' => 'edit staff']);
        Permission::firstOrCreate(['name' => 'delete staff']);

        // Permission for Room Users
        Permission::firstOrCreate(['name' => 'view room users']);
        Permission::firstOrCreate(['name' => 'create room users']);
        Permission::firstOrCreate(['name' => 'edit room users']);
        Permission::firstOrCreate(['name' => 'delete room users']);

        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Room Manager']);

        // Assign permissions
        $adminRole->syncPermissions(Permission::all());

        // Room Manager can only view + edit, no create
        $managerRole->givePermissionTo(['view rooms', 'edit rooms', 'view staff', 'edit staff', 'view room users', 'edit room users']);

        // Default Admin
        $admin = User::firstOrCreate(
            ['email' => 'mdcpathfinder@gmail.com'], // required for admin
            [
                'name' => 'Administrator',           // optional but nice to have
                'username' => 'Admin',                  // admin doesn't need username
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('iamp@thfinderadmin'),
            ]
        );
        $admin->assignRole($adminRole);
    }
}
