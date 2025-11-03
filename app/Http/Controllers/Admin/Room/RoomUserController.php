<?php

namespace App\Http\Controllers\Admin\Room;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RoomUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy',
            'restore',
            'forceDelete',
            'toggleStatus'
        ]);
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();
        $roomId = $request->query('roomId');

        // Decide rooms for dropdown
        $rooms = match (true) {
            $authUser->hasRole('Admin') => Room::all(),
            $authUser->hasRole('Office Manager') && $authUser->room_id => Room::where('id', $authUser->room_id)->get(),
            default => collect(),
        };

        $userQuery = User::with('room');

        if ($authUser->hasRole('Admin') && $roomId) {
            $userQuery->where('room_id', $roomId);
        } elseif ($authUser->hasRole('Office Manager')) {
            if (!$authUser->room_id) {
                $userQuery->where('id', $authUser->id);
            } else {
                $userQuery->where('room_id', $authUser->room_id);
                if ($roomId && $roomId != $authUser->room_id) {
                    abort(403, 'Unauthorized office access.');
                }
            }
        } elseif ($authUser->room_id) {
            $userQuery->where('room_id', $authUser->room_id);
        }

        $users = $userQuery->paginate(10)->withQueryString();

        return view('pages.admin.room-users.index', compact('users', 'rooms', 'roomId'));
    }

    public function create(Request $request)
    {
        $rooms = Room::doesntHave('officeManager')->get();
        $roomId = $request->query('roomId') ?? ($rooms->first()->id ?? null);
        $selectedRoom = $roomId ? Room::find($roomId) : null;

        $this->authorize('create', $selectedRoom);

        $staff = Staff::with('room')->paginate(10);

        return view('pages.admin.room-users.create', compact('rooms', 'staff', 'selectedRoom'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:20',
            'username' => 'required|string|max:20|unique:users,username',
            'password' => 'required|string|confirmed|min:8',
            'room_id' => [
                'required',
                'exists:rooms,id',
                function ($attr, $value, $fail) {
                    if (User::where('room_id', $value)->exists()) {
                        $fail('This office already has an assigned Office Manager.');
                    }
                }
            ],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'room_id' => $request->room_id,
        ]);

        $user->assignRole('Office Manager');

        return redirect()->route('room-user.index')->with('success', 'Office user created successfully!');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $rooms = Room::whereDoesntHave('officeManager')
            ->orWhere('id', $user->room_id)
            ->get();

        $selectedRoom = $user->room;

        return view('pages.admin.room-users.edit', compact('user', 'rooms', 'selectedRoom'));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return view('pages.admin.room-users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'nullable|string|max:20',
            'username' => 'required|string|max:20|unique:users,username,' . $user->id,
            'password' => 'nullable|string|confirmed|min:8',
            'room_id' => [
                'nullable',
                'exists:rooms,id',
                function ($attr, $value, $fail) use ($user) {
                    if ($value && User::where('room_id', $value)->where('id', '!=', $user->id)->exists()) {
                        $fail('This office already has an assigned Office Manager.');
                    }
                }
            ],
        ]);

        $data = ['name' => $request->name, 'username' => $request->username];

        if ($user->hasRole('Admin')) {
            $data['room_id'] = null;
            if ($request->filled('room_id')) {
                return back()->with('error', 'Admins cannot be assigned to any office.');
            }
        } else {
            $data['room_id'] = $request->room_id ?: null;
        }

        $passwordChanged = false;
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $passwordChanged = true;
        }

        $user->update($data);

        if ($passwordChanged) {
            $user->clearSessions();

            if (Auth::id() === $user->id) {
                Auth::logout();
                return redirect()->route('login')->with('success', 'Your password was changed. Please log in again.');
            }

            return redirect()->route('room-user.index')->with('success', 'User password updated successfully. That user has been logged out.');
        }

        return redirect()->route('room-user.index')->with('success', 'Office user updated successfully!');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->hasRole('Admin') && User::role('Admin')->count() === 1) {
            return back()->with('error', 'You cannot delete the last remaining Admin.');
        }

        $user->clearSessions();
        $user->delete();

        return redirect()->route('room-user.index')->with('success', 'Office user deleted successfully. Data has been archived.');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->clearSessions();
        $user->restore();

        return redirect()->route('recycle-bin')->with('success', 'Office user restored successfully.');
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $this->authorize('delete', $user);

        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot permanently delete your own account.');
        }

        if ($user->hasRole('Admin') && User::role('Admin')->count() <= 1) {
            return back()->with('error', 'You cannot delete the last remaining Admin.');
        }

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->forceDelete();

        return redirect()->route('recycle-bin')->with('success', 'Office user permanently deleted.');
    }

    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);

        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot disable your own account.');
        }

        if ($user->hasRole('Admin') && User::role('Admin')->count() === 1) {
            return back()->with('error', 'You cannot disable the last remaining Admin.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        if (!$user->is_active) {
            $user->clearSessions();
        }

        return redirect()->route('room-user.index')->with('success', "User {$user->name} has been " . ($user->is_active ? 'enabled' : 'disabled') . ".");
    }
}
