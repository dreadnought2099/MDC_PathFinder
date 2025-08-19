<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

    public function store(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'room_id'   => 'nullable|exists:rooms,id',
            'first_name'    => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'    => 'required|string|max:255',
            'suffix'    => 'nullable|string|max:50',
            'credentials'    => 'nullable|string|max:255',
            'position'  => 'nullable|string|max:255',
            'bio'       => 'nullable|string',
            'email'     => 'nullable|email|max:255|unique:staff,email',
            'phone_num' => 'nullable|string|max:20',
            'photo_path' => 'nullable|image|max:51200',
        ]);

        // Create staff first
        $staff = Staff::create($validated);

        // Then handle photo upload
        if ($request->hasFile('photo_path')) {
            $path = $request->file('photo_path')
                ->store('staffs/' . $staff->id, 'public');
            $staff->update(['photo_path' => $path]);
        }

        session()->flash('success', "{$staff->full_name} was added successfully.");

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('staff.show', $staff->id)], 200);
        }

        return redirect()->route('staff.index')
            ->with('success', "{$staff->full_name} was added successfully.");
    }

    public function show(Staff $staff)
    {
        return view('pages.admin.staffs.show', compact('staff'));
    }

    public function clientShow(Staff $staff)
    {
        return view('pages.client.client-show', compact('staff'));
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
            'first_name'    => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'    => 'required|string|max:255',
            'suffix'    => 'nullable|string|max:50',
            'credentials'    => 'nullable|string|max:255',
            'position'  => 'nullable|string|max:255',
            'bio'       => 'nullable|string',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('staff', 'email')->ignore($staff->id),
            ],
            'phone_num' => 'nullable|string|max:20',
            'photo_path' => 'nullable|image|max:51200',
        ]);

        // Remove photo_path from validated data to handle it separately
        unset($validated['photo_path']);

        // Handle photo upload
        if ($request->hasFile('photo_path')) {
            // Delete old photo if exists
            if ($staff->photo_path && Storage::disk('public')->exists($staff->photo_path)) {
                Storage::disk('public')->delete($staff->photo_path);
            }

            // Store new photo under staffs/{staff_id}/
            $path = $request->file('photo_path')
                ->store('staffs/' . $staff->id, 'public');

            // Add the new path to the data to be updated
            $validated['photo_path'] = $path;
        }

        // Update staff with all validated data (including photo_path if uploaded)
        $staff->update($validated);

        session()->flash('success', "{$staff->full_name} updated successfully.");

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('staff.show', $staff->id)], 200);
        }

        return redirect()->route('staff.index')->with('success', "{$staff->full_name} updated successfully.");
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();
        return redirect()->route('staff.index')->with('success', "{$staff->full_name} moved to recycle bin");
    }

    public function restore($id)
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);
        $staff->restore();
        return redirect()->route('staff.recycle-bin')->with('success', "{$staff->full_name} restored successfully.");
    }

    public function forceDelete($id)
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);

        if (Storage::disk('public')->exists('staffs/' . $staff->id)) {
            Storage::disk('public')->deleteDirectory('staffs/' . $staff->id);
        }

        $staff->forceDelete();

        return redirect()->route('staff.recycle-bin')->with('success', "{$staff->full_name} permanently deleted.");
    }
}
