<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoomPolicy
{
    /**
     * Determine if the user can view a room.
     */
    public function view(User $user, Room $room): Response
    {
        return $user->hasPermissionTo('view rooms') &&
            ($user->hasRole('Admin') || $user->room_id === $room->id)
            ? Response::allow()
            : Response::deny('You cannot view this room.');
    }

    /**
     * Determine if the user can create a room.
     */
    public function create(User $user): Response
    {
        return $user->hasRole('Admin') || $user->hasPermissionTo('create rooms')
            ? Response::allow()
            : Response::deny('You are not allowed to create rooms.');
    }

    /**
     * Determine if the user can update a room.
     */
    public function update(User $user, Room $room): Response
    {
        return $user->hasPermissionTo('edit rooms') &&
            ($user->hasRole('Admin') || $user->room_id === $room->id)
            ? Response::allow()
            : Response::deny('You are not allowed to update this room.');
    }

    /**
     * Determine if the user can delete a room.
     */
    public function delete(User $user, Room $room): Response
    {
        return $user->hasPermissionTo('delete rooms') && $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('Only Admins can delete rooms.');
    }

    /**
     * Determine if the user can assign staff to a room.
     */
    public function assignStaff(User $user, Room $room): Response
    {
        return $user->hasPermissionTo('edit staff') &&
            ($user->hasRole('Admin') || $user->room_id === $room->id)
            ? Response::allow()
            : Response::deny('You cannot assign staff to this room.');
    }

    /**
     * Determine if the Admin can create room user account to a room.
     */

    public function createUser(User $user, Room $room): Response
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to create room user accounts.');
    }
}
