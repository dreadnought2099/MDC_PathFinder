<?php

namespace App\Policies;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StaffPolicy
{
    /**
     * Determine if the user can view a staff member.
     */
    public function view(User $user, Staff $staff): Response
    {
        return $user->hasPermissionTo('view staff') &&
            ($user->hasRole('Admin') || $user->room_id === $staff->room_id)
            ? Response::allow()
            : Response::deny('You cannot view this staff member.');
    }

    /**
     * Determine if the user can create staff.
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create staff')
            ? Response::allow()
            : Response::deny('You are not allowed to create staff.');
    }

    /**
     * Determine if the user can update a staff member.
     */
    public function update(User $user, Staff $staff): Response
    {
        return $user->hasPermissionTo('edit staff') &&
            ($user->hasRole('Admin') || $user->room_id === $staff->room_id)
            ? Response::allow()
            : Response::deny('You are not allowed to update this staff member.');
    }

    /**
     * Determine if the user can delete a staff member.
     */
    public function delete(User $user, Staff $staff): Response
    {
        return $user->hasPermissionTo('delete staff') && $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('Only Admins can delete staff members.');
    }
}