<?php

namespace App\Http\Controllers\Admin\Room;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RoomUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin'])->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy',
            'restore',
            'forceDelete'
        ]);
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();

        // Decide which rooms to show in dropdown
        if ($authUser->hasRole('Admin')) {
            $rooms = Room::all();
        } elseif ($authUser->hasRole('Office Manager')) {
            if ($authUser->room_id) {
                // Only their assigned office
                $rooms = Room::where('id', $authUser->room_id)->get();
            } else {
                // No assigned office → no dropdown at all
                $rooms = collect();
            }
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
        } elseif ($authUser->hasRole('Office Manager')) {
            if (is_null($authUser->room_id)) {
                // No assigned office → show only themselves
                $userQuery->where('id', $authUser->id);
            } else {
                // Show users from their assigned office only
                $userQuery->where('room_id', $authUser->room_id);

                // Prevent tampering (?roomId=999)
                if ($roomId && $roomId != $authUser->room_id) {
                    abort(403, 'Unauthorized office access.');
                }
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
        // Get IDs of already assigned rooms
        $assignedRoomIds = User::whereNotNull('room_id')->pluck('room_id')->toArray();

        // Show only unassigned rooms
        $rooms = Room::whereNotIn('id', $assignedRoomIds)->get();

        // Determine selected room: query parameter first, fallback to first room
        $roomId = $request->query('roomId') ?? ($rooms->first()->id ?? null);
        $selectedRoom = $roomId ? Room::find($roomId) : null;

        // Authorization
        $this->authorize('create', $selectedRoom);

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

        $existingUser = User::where('room_id', $request->room_id)->first();

        if ($existingUser) {
            return back()->withInput()->with('error', 'This office already has an assigned Office Manager.');
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'room_id' => $request->room_id,
        ]);

        // Assign Office Manager role
        $user->assignRole('Office Manager');

        return redirect()->route('room-user.index')
            ->with('success', 'Ofice user created successfully!');
    }

    public function edit(User $user)
    {
        // Load rooms for dropdown selection
        $allRooms = Room::all();

        // Get IDs of rooms that already have an office manager (excluding current user's room)
        $assignedRoomIds = User::whereNotNull('room_id')
            ->where('id', '!=', $user->id)
            ->pluck('room_id')
            ->toArray();

        // Filter out already assigned rooms 
        // uses a collection filter instead of reject() for readability:
        $rooms = $allRooms->filter(fn($room) => !in_array($room->id, $assignedRoomIds));

        // Authorization (optional, if using policies)
        $this->authorize('update', $user);

        return view('pages.admin.room-users.edit', compact('user', 'rooms'));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return view('pages.admin.room-users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'nullable|string|max:20',
            'username' => 'required|string|max:20|unique:users,username,' . $user->id,
            'password' => 'nullable|string|confirmed|min:8',
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        // Prepare data for update
        $data = [
            'name' => $request->name,
            'username' => $request->username,
        ];

        // Handle room assignment
        if ($user->hasRole('Admin')) {
            // Prevent Admin from being assigned any room
            $data['room_id'] = null;
            if ($request->filled('room_id')) {
                return back()->with('error', 'Admins cannot be assigned to any office.');
            }
        } else {
            // Check if room_id is being changed
            if ($request->filled('room_id') && $request->room_id != $user->room_id) {
                // Check if another user is already assigned to this room
                $existingUser = User::where('room_id', $request->room_id)
                    ->where('id', '!=', $user->id)
                    ->first();

                if ($existingUser) {
                    return back()->withInput()->with('error', 'This office already has an assigned Office Manager.');
                }
            }

            $data['room_id'] = $request->room_id ?: null;
        }

        // Handle password update
        $passwordChanged = false;
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $passwordChanged = true;
        }

        $user->update($data);

        // If password changed, log out sessions
        if ($passwordChanged) {
            DB::table('sessions')->where('user_id', $user->id)->delete();

            if (Auth::id() === $user->id) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('success', 'Your password was changed. Please log in again.');
            }

            return redirect()->route('room-user.index')
                ->with('success', 'User password updated successfully. That user has been logged out.');
        }

        return redirect()->route('room-user.index')
            ->with('success', 'Office user updated successfully!');
    }

    /**
     * Soft delete a room user
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting their own account
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->hasRole('Admin') && User::role('Admin')->count() === 1) {
            return back()->with('error', 'You cannot delete the last remaining Admin.');
        }

        // Clear all active sessions for this user
        DB::table('sessions')->where('user_id', $user->id)->delete();

        // Clear remember token
        $user->update(['remember_token' => null]);

        // Soft delete the room user
        $user->delete();

        return redirect()->route('room-user.index')
            ->with('success', 'Office user deleted successfully. Data has been archived.');
    }

    /**
     * Restore a soft-deleted room user
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);

        if ($user) {
            // Clear any existing sessions before restoring
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();

            // Clear remember token to force fresh login
            $user->remember_token = null;
            $user->save();

            $user->restore();

            return redirect()->route('recycle-bin')
                ->with('success', 'Office user restored successfully.');
        }

        return redirect()->route('recycle-bin')
            ->with('error', 'Office user not found or not deleted.');
    }

    /**
     * Permanently delete a soft-deleted room user
     */
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return redirect()->route('recycle-bin')
                ->with('error', 'Office user has not been soft deleted or does not exist.');
        }

        // Prevent admin from force-deleting their own account
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot permanently delete your own account.');
        }

        // Prevent deleting last Admin
        if (method_exists($user, 'hasRole') && $user->hasRole('Admin')) {
            $adminCount = User::role('Admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'You cannot delete the last remaining Admin.');
            }
        }

        // Delete profile photo if exists
        try {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
        } catch (\Exception $e) {
            Log::error("Error deleting profile photo: " . $e->getMessage());
        }

        $user->forceDelete();

        return redirect()->route('recycle-bin')
            ->with('success', 'Office user permanently deleted.');
    }

    /**
     * Toggle a user's active status (enable/disable account).
     */
    public function toggleStatus(User $user)
    {
        // Prevent admin from disabling their own account
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot disable your own account.');
        }

        if ($user->hasRole('Admin') && User::role('Admin')->count() === 1) {
            return back()->with('error', 'You cannot delete the last remaining Admin.');
        }

        // Flip the status: if active → disable, if disabled → enable
        $user->is_active = !$user->is_active;
        $user->save();

        // If user is being disabled, kill all their active sessions
        if (!$user->is_active) {
            // Laravel stores sessions in the "sessions" table (if using database driver)
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()->route('room-user.index')->with(
            'success',
            "User {$user->name} has been " . ($user->is_active ? 'enabled' : 'disabled') . "."
        );
    }
}
