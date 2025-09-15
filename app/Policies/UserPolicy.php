<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine if the user can create room user accounts.
     */
    public function create(User $user): Response
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to create room user accounts.');
    }

    /**
     * Determine if the user can update (edit) a room user account.
     */
    public function update(User $authUser, User $model): Response
    {
        return ($authUser->hasRole('Admin') || $authUser->can('edit room users'))
            ? Response::allow()
            : Response::deny('You do not have permission to edit room user accounts.');
    }

    /**
     * Determine if the user can delete a room user account.
     */
    public function delete(User $authUser, User $model): Response
    {
        // Admins can delete anyone
        if ($authUser->hasRole('Admin')) {
            return Response::allow();
        }

        // Room Managers can delete users only in their own room
        if ($authUser->hasRole('Room Manager') && $authUser->room_id === $model->room_id) {
            return Response::allow();
        }

        // Otherwise, deny
        return Response::deny('You do not have permission to delete this user.');
    }
}