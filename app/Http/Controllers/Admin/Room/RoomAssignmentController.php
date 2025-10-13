<?php

namespace App\Http\Controllers\Admin\Room;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Staff;
use Illuminate\Http\Request;

class RoomAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin'])->only(['assign', 'assignStaff', 'removeFromRoom']);
    }

    public function assign(Request $request, $roomId = null)
    {
        $rooms = Room::all();
        $roomId = $roomId ?? $request->query('roomId') ?? ($rooms->first()->id ?? null);
        $selectedRoom = $roomId ? Room::find($roomId) : null;

        // Get search from request or session
        $search = $request->input('search', session('assign.search', ''));

        // Store in session
        session(['assign.search' => $search]);

        $staff = Staff::with('room')
            ->when(!empty($search), fn($q) => $q->where('full_name', 'like', "%{$search}%"))
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('pages.admin.rooms.partials.staff-assignment', compact('staff', 'selectedRoom', 'search'))->render(),
            ]);
        }

        return view('pages.admin.rooms.assign', compact('rooms', 'staff', 'selectedRoom', 'search'));
    }

    public function assignStaff(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:staff,id',
        ]);

        $roomId = $request->room_id;
        $staffIds = $request->staff_ids ?? [];

        $currentlyAssigned = Staff::where('room_id', $roomId)->pluck('id')->toArray();
        $toUnassign = array_diff($currentlyAssigned, $staffIds);
        $toAssign = array_diff($staffIds, $currentlyAssigned);

        if (!empty($toUnassign)) {
            Staff::whereIn('id', $toUnassign)->update(['room_id' => null]);
        }
        if (!empty($toAssign)) {
            Staff::whereIn('id', $toAssign)->update(['room_id' => $roomId]);
        }

        $room = Room::findOrFail($roomId);
        $assignedStaff = Staff::whereIn('id', $toAssign)
            ->get()
            ->map(fn($s) => $s->full_name)
            ->toArray();

        $staffNames = !empty($assignedStaff) ? implode(', ', $assignedStaff) : 'No staff';
        $message = "{$staffNames} was successfully assigned to {$room->name}.";

        // Check if AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        // Fallback for non-AJAX
        return redirect()
            ->route('room.assign', ['roomId' => $room->id])
            ->with('success', $message);
    }

    public function removeFromRoom(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $room = $staff->room;

        if (!$room) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff is not assigned to any room.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Staff is not assigned to any room.');
        }

        $name = $staff->full_name;
        $roomName = $room->name;
        $roomId = $room->id;

        $staff->room_id = null;
        $staff->save();

        $message = "{$name} was successfully removed from {$roomName}.";

        // Check if AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        // Fallback for non-AJAX
        return redirect()
            ->route('room.assign', ['roomId' => $roomId])
            ->with('success', $message);
    }
}
