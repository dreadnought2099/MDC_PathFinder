<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Grant Admins full access automatically
     */
    public function before(User $authUser, $ability)
    {
        if ($authUser->hasRole('Admin')) {
            return Response::allow();
        }
    }

    /**
     * Determine if the user can create a room user account.
     */
    public function create(User $authUser, Room $room = null): Response
    {
        // Only Admins can create users
        return Response::deny('You do not have permission to create office user accounts.');
    }

    /**
     * Determine if the user can view a room user account.
     */
    public function view(User $authUser, User $model): Response
    {
        if ($authUser->hasRole('Office Manager') && $authUser->id === $model->id) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this user.');
    }

    /**
     * Determine if the user can update a room user account.
     */
    public function update(User $authUser, User $model): Response
    {
        return Response::deny('You do not have permission to edit this user.');
    }

    /**
     * Determine if the user can delete a room user account.
     */
    public function delete(User $authUser, User $model): Response
    {
        return Response::deny('You do not have permission to delete this user.');
    }
}