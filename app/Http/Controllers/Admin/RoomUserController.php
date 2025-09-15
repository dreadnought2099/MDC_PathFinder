<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RoomUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:create room users'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit room users'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete room users'])->only(['destroy']);
    }

    // Show form
    public function create(Request $request)
    {
        $rooms = Room::all();

        // Determine selected room: query parameter first, fallback to first room
        $roomId = $request->query('roomId') ?? ($rooms->first()->id ?? null);
        $selectedRoom = $roomId ? Room::find($roomId) : null;

        // Authorization
        $this->authorize('createUser', $selectedRoom);

        // Staff list (paginated if needed)
        $staff = Staff::with('room')->paginate(12);

        return view('pages.admin.room-users.create', compact('rooms', 'staff', 'selectedRoom'));
    }


    // Store new room user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:20',
            'username' => 'required|string|max:20|unique:users,username',
            'password' => 'required|string|confirmed|min:8',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $user = User::create([
            'name' => $request->name,
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
