<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {

        $staffs = Staff::all();
        return view('staffs.index', compact('staffs'));
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
}
