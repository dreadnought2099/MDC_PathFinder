<?php

namespace App\Http\Controllers\Admin;

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
        $search = $request->get('search');

        $staff = Staff::with('room')
            ->when($search, fn($q) =>
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%"))
            ->paginate(12)
            ->appends(['search' => $search]);

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
        $page = $request->input('page', 1);

        return redirect()
            ->route('room.assign', ['roomId' => $room->id])
            ->with('success', "{$staffNames} was successfully assigned to {$room->name}.")
            ->withInput(['roomId' => $room->id, 'page' => $page]);
    }

    public function removeFromRoom(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $room = $staff->room;
        $name = $staff->full_name;

        $staff->room_id = null;
        $staff->save();

        $page = $request->input('page') ?? session('current_page', 1);

        return redirect()
            ->route('room.assign', ['roomId' => $room->id])
            ->with('success', "{$name} was successfully removed from {$room->name}.")
            ->withInput(['roomId' => $room->id, 'page' => $page]);
    }
}
