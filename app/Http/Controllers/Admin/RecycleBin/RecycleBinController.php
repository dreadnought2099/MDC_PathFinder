<?php

namespace App\Http\Controllers\Admin\RecycleBin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;

class RecycleBinController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Rooms - Admin only
        $rooms = $user->hasRole('Admin')
            ? Room::onlyTrashed()->get()
            : collect();


        // Staff - Admin sees all, Office Manager sees only their office's staff
        $staffs = $user->hasRole('Admin')
            ? Staff::onlyTrashed()->get()
            : ($user->hasRole('Office Manager') && $user->room_id
                ? Staff::onlyTrashed()->where('room_id', $user->room_id)->get()
                : collect());

        // Admin only
        $users = $user->hasRole('Admin')
            ? User::onlyTrashed()->get()
            : collect();

        $feedback = $user->hasRole('Admin')
            ? Feedback::onlyTrashed()->get()
            : collect();

        return view('pages.admin.recycle-bin', compact('rooms', 'staffs', 'users', 'feedback'));
    }
}
