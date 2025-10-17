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

        $search = $request->input('search', session('assign.search', ''));
        session(['assign.search' => $search]);

        $staff = Staff::with('room')
            ->when(!empty($search), fn($q) => $q->where('full_name', 'like', "%{$search}%"))
            ->orderByRaw("
            CASE 
                WHEN room_id = ? THEN 0
                WHEN room_id IS NULL THEN 1
                ELSE 2
            END
        ", [$roomId])
            ->orderBy('full_name') // Secondary sort by name
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

        // Get currently assigned staff for THIS room only
        $currentlyAssigned = Staff::where('room_id', $roomId)->pluck('id')->toArray();

        // Find staff to assign (checked but not currently assigned to this room)
        $toAssign = array_diff($staffIds, $currentlyAssigned);

        // Assign new staff members only
        if (!empty($toAssign)) {
            Staff::whereIn('id', $toAssign)->update(['room_id' => $roomId]);
        }

        $room = Room::findOrFail($roomId);

        // Prepare success message
        if (!empty($toAssign)) {
            $assignedStaff = Staff::whereIn('id', $toAssign)
                ->get()
                ->map(fn($s) => $s->full_name)
                ->toArray();

            $staffNames = implode(', ', $assignedStaff);
            $message = "{$staffNames} " . (count($assignedStaff) > 1 ? 'were' : 'was') . " successfully assigned to {$room->name}.";
        } else {
            $message = "No new staff members to assign.";
        }

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
