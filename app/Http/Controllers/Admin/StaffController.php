<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = Staff::with('room')->get();
        return view('pages.admin.staffs.index', compact('staffs'));
    }

    public function create()
    {
        $rooms = Room::all();
        return view('pages.admin.staffs.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'   => 'nullable|exists:rooms,id',
            'name'      => 'required|string|max:255',
            'position'  => 'nullable|string|max:255',
            'bio'       => 'nullable|string',
            'email'     => 'nullable|email|max:255',
            'phone_num' => 'nullable|string|max:20',
            'photo_path'     => 'nullable|image|max:51200',
        ]);

        if ($request->hasFile('photo_path')) {
            $validated['photo_path'] = $request->file('photo_path')->store('staff_photos', 'public');
        }

        Staff::create($validated);

        return redirect()->route('staff.index')->with('success', 'Staff added successfully.');
    }

    public function show(Staff $staff)
    {
        return view('pages.admin.staffs.show', compact('staff'));

    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        $rooms = Room::all();
        return view('pages.admin.staffs.edit', compact('staff', 'rooms'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'room_id'   => 'nullable|exists:rooms,id',
            'name'      => 'required|string|max:255',
            'position'  => 'nullable|string|max:255',
            'bio'       => 'nullable|string',
            'email'     => 'nullable|email|max:255',
            'phone_num' => 'nullable|string|max:20',
            'photo_path'     => 'nullable|image|max:51200',
        ]);

        if ($request->hasFile('photo_path')) {
            // Delete old photo if exists
            if ($staff->photo_path && Storage::disk('public')->exists($staff->photo_path)) {
                Storage::disk('public')->delete($staff->photo_path);
            }
            $validated['photo_path'] = $request->file('photo_path')->store('staff_photos', 'public');
        }

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff member moved to trash.');
    }

    public function trashed()
    {
        $staffs = Staff::onlyTrashed()->with('room')->get();
        return view('pages.admin.staffs.trashed', compact('staffs'));
    }

    public function restore($id)
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);
        $staff->restore();
        return redirect()->route('staff.trashed')->with('success', 'Staff member restored successfully.');
    }

    public function forceDelete($id)
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);

        if ($staff->photo_path && Storage::disk('public')->exists($staff->photo_path)) {
            Storage::disk('public')->delete($staff->photo_path);
        }

        $staff->forceDelete();

        return redirect()->route('staff.trashed')->with('success', 'Staff member permanently deleted.');
    }
}
