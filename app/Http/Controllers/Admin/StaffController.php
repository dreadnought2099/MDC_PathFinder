<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'last_name');   // default sort column
        $direction = $request->get('direction', 'asc'); // default direction

        $staffs = Staff::with('room')
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->appends(['sort' => $sort, 'direction' => $direction]); // keep params in pagination links

        return view('pages.admin.staffs.index', compact('staffs', 'sort', 'direction'));
    }

    public function create()
    {
        return view('pages.admin.staffs.create');
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
            'photo_path' => 'nullable|image|max:5120',
        ]);

        // Create staff first
        $staff = Staff::create($validated);
        $manager = new ImageManager(new Driver());

        // Then handle photo upload
        if ($request->hasFile('photo_path')) {

            $baseName = uniqid('', true);
            $folder   = "staffs/{$staff->id}";
            $webpPath = "{$folder}/{$baseName}.webp";

            $image = $manager->read($request->file('photo_path'))->encode(new WebpEncoder(90));
            Storage::disk('public')->put($webpPath, (string) $image);

            $staff->update(['photo_path' => $webpPath]);
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
        return view('pages.client.room-details.client-show', compact('staff'));
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        $rooms = Room::all();
        $this->authorize('update', $staff); // uses StaffPolicy
        return view('pages.admin.staffs.edit', compact('staff', 'rooms'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        // Authorization
        $this->authorize('update', $staff); // uses StaffPolicy

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
            'photo_path' => 'nullable|image|max:5120',
        ]);

        // Remove photo_path from validated data to handle it separately
        unset($validated['photo_path']);
        $manager = new ImageManager(new Driver());

        // Handle photo upload
        if ($request->hasFile('photo_path')) {
            // Delete old photo if exists
            if ($staff->photo_path && Storage::disk('public')->exists($staff->photo_path)) {
                Storage::disk('public')->delete($staff->photo_path);
            }

            // Store new photo under staffs/{staff_id}/
            $baseName = uniqid('', true);
            $folder   = "staffs/{$staff->id}";
            $webpPath = "{$folder}/{$baseName}.webp";

            $image = $manager->read($request->file('photo_path'))->encode(new WebpEncoder(90));
            Storage::disk('public')->put($webpPath, (string) $image);

            // Add the new path to the data to be updated
            $validated['photo_path'] = $webpPath;
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
        return redirect()->route('recycle-bin')->with('success', "{$staff->full_name} restored successfully.");
    }

    public function forceDelete($id)
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);

        if (Storage::disk('public')->exists('staffs/' . $staff->id)) {
            Storage::disk('public')->deleteDirectory('staffs/' . $staff->id);
        }

        $staff->forceDelete();

        return redirect()->route('recycle-bin')->with('success', "{$staff->full_name} permanently deleted.");
    }
}
