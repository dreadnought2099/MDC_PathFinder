<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RoomUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage rooms']);
    }

    // Show form
    public function create()
    {
        $rooms = Room::all();
        return view('pages.admin.room-users.create', compact('rooms'));
    }

    // Store new room user
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|confirmed|min:8',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'room_id' => $request->room_id,
        ]);

        // Assign Room Manager role
        $user->assignRole('Room Manager');

        return redirect()->route('room.index')
            ->with('success', 'Room user created successfully!');
    }
}
