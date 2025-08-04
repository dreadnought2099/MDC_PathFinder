<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index()
    {

        $staffs = Staff::all();
        return view('pages.admin.staffs.index', compact('staffs'));
    }

    public function create()
    {

        return view('pages.admin.staffs.create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255|unique:staff,email',
            'phone_num' => ['nullable', 'regex:/^09\d{9}$/'],
            'photo_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Handle image upload if present
        if ($request->hasFile('photo_path')) {
            $validated['photo_path'] = $request->file('photo_path')->store('staff_photos', 'public');
        }

        Staff::create($validated);

        return redirect()->route('staff.index')->with('success', 'Staff created successfully.');
    }

    public function show(Staff $staff)
    {
        return view('pages.admin.staffs.show', compact('staff'));

        return redirect()->route('staff.show', $staff->id)->with('success', 'Staff created successfully.');
    }

    public function edit(Staff $staff)
    {
        return view('pages.admin.staffs.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255|unique:staff,email,' . $staff->id,
            'phone_num' => ['nullable', 'regex:/^09\d{9}$/'],
            'photo_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('photo_path')) {
            // Delete old image if exists
            if ($staff->photo_path && Storage::disk('public')->exists($staff->photo_path)) {
                Storage::disk('public')->delete($staff->photo_path);
            }

            $validated['photo_path'] = $request->file('photo_path')->store('staff_photos', 'public');
        }

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        // Delete photo if it exists
        if ($staff->photo_path && Storage::disk('public')->exists($staff->photo_path)) {
            Storage::disk('public')->delete($staff->photo_path);
        }

        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff deleted successfully.');
    }
}
