<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RoomUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:create room users'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit room users'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete room users'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();

        // Decide which rooms to show in dropdown
        if ($authUser->hasRole('Admin')) {
            $rooms = Room::all();
        } elseif ($authUser->hasRole('Room Manager')) {
            $rooms = Room::where('id', $authUser->room_id)->get();
        } else {
            $rooms = collect(); // no dropdown for other roles
        }

        $roomId = $request->query('roomId');
        $userQuery = User::with('room');

        if ($authUser->hasRole('Admin')) {
            // Admins can filter any room
            if ($roomId) {
                $userQuery->where('room_id', $roomId);
            }
        } elseif ($authUser->hasRole('Room Manager')) {
            // Room Managers can only ever see their own room
            $userQuery->where('room_id', $authUser->room_id);

            // Block URL tampering (?roomId=999)
            if ($roomId && $roomId != $authUser->room_id) {
                abort(403, 'Unauthorized room access.');
            }
        } else {
            // Other users: show only their own room
            if ($authUser->room_id) {
                $userQuery->where('room_id', $authUser->room_id);
            }
        }

        $users = $userQuery->paginate(10)->withQueryString();

        return view('pages.admin.room-users.index', compact('users', 'rooms', 'roomId'));
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
        $staff = Staff::with('room')->paginate(10);

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

        return redirect()->route('room-user.index')
            ->with('success', 'Room user created successfully!');
    }

    public function edit(User $user)
    {
        // Load rooms for dropdown selection
        $rooms = Room::all();

        // Authorization (optional, if using policies)
        $this->authorize('update', $user);

        return view('pages.admin.room-users.edit', compact('user', 'rooms'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'nullable|string|max:20',
            'username' => 'required|string|max:20|unique:users,username,' . $user->id,
            'password' => 'nullable|string|confirmed|min:8', // only required if changing password
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'room_id' => $request->room_id ?: null,
        ];

        // Update password only if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('room-user.index')
            ->with('success', 'Room user updated successfully!');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('room-user.index')
            ->with('success', 'Room user deleted successfully!');
    }
}
