<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{

    /**
     * Determine if the user can view a room user account.
     */
    public function view(User $authUser, User $model): Response
    {
        // Admins can view any user
        if ($authUser->hasRole('Admin')) {
            return Response::allow();
        }

        // Office Managers can view users only within their assigned room
        if ($authUser->hasRole('Office Manager') && $authUser->room_id === $model->room_id) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this user.');
    }

    /**
     * Determine if the user can create room user accounts.
     */
    public function create(User $user): Response
    {
        return $user->hasRole('Admin')
            ? Response::allow()
            : Response::deny('You do not have permission to create office user accounts.');
    }

    /**
     * Determine if the user can update (edit) a room user account.
     */
    public function update(User $authUser, User $model): Response
    {
        // Admins can edit anyone
        if ($authUser->hasRole('Admin')) {
            return Response::allow();
        }

        // Office Managers can edit users in their assigned room
        if ($authUser->hasRole('Office Manager') && $authUser->room_id === $model->room_id) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to edit this user.');
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

        // Office Managers can delete users only in their own room
        if ($authUser->hasRole('Office Manager') && $authUser->room_id === $model->room_id) {
            return Response::allow();
        }

        // Otherwise, deny
        return Response::deny('You do not have permission to delete this user.');
    }
}
