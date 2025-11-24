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
        // Admin can view any staff
        if ($user->hasRole('Admin') && $user->hasPermissionTo('view staff')) {
            return Response::allow();
        }

        // Office Manager can only view staff in their assigned office
        if (
            $user->hasRole('Office Manager') &&
            $user->hasPermissionTo('view staff') &&
            $user->room_id &&
            $user->room_id === $staff->room_id
        ) {
            return Response::allow();
        }

        return Response::deny('You cannot view this staff member.');
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
        // Admin can update any staff
        if ($user->hasRole('Admin') && $user->hasPermissionTo('edit staff')) {
            return Response::allow();
        }

        // Office Manager can only update staff in their assigned office
        if (
            $user->hasRole('Office Manager') &&
            $user->hasPermissionTo('edit staff') &&
            $user->room_id &&
            $user->room_id === $staff->room_id
        ) {
            return Response::allow();
        }

        return Response::deny('You are not allowed to update this staff member.');
    }

    /**
     * Determine if the user can delete a staff member.
     */
    public function delete(User $user, Staff $staff): Response
    {
        // Admin can delete any staff
        if ($user->hasRole('Admin') && $user->hasPermissionTo('delete staff')) {
            return Response::allow();
        }

        // Office Manager can only delete staff in their assigned office
        if (
            $user->hasRole('Office Manager') &&
            $user->hasPermissionTo('delete staff') &&
            $user->room_id &&
            $user->room_id === $staff->room_id
        ) {
            return Response::allow();
        }

        return Response::deny('You are not allowed to delete this staff member.');
    }
}
