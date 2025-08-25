<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::paginate(10);
        return view('pages.admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $staffs = Staff::all();
        return view('pages.admin.rooms.create', compact('staffs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_path' => 'nullable|image|max:51200',
            'video_path' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:102400',
            'carousel_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:102400',

            // Office hours
            'office_hours' => 'nullable|array',
        ]);

        $roomData = collect($validated)->except('office_hours')->toArray();
        $room = Room::create($roomData);

        if ($request->has('office_hours')) {
            foreach ($request->office_hours as $day => $ranges) {
                foreach ($ranges as $range) {
                    if (!empty($range['start']) && !empty($range['end'])) {
                        $room->officeHours()->create([
                            'day'        => $day,
                            'start_time' => $range['start'],
                            'end_time'   => $range['end'],
                        ]);
                    }
                }
            }
        }

        // Cover image
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('rooms/' . $room->id . '/cover_images', 'public');
            $room->image_path = $path;
            $room->save();
        }

        // Video
        if ($request->hasFile('video_path')) {
            $path = $request->file('video_path')->store('rooms/' . $room->id . '/videos', 'public');
            $room->video_path = $path;
            $room->save();
        }

        // Generate QR code
        $marker_id = 'room_' . $room->id;
        $qrImage = QrCode::format('svg')->size(300)->generate($room->id);
        $qrPath = 'rooms/' . $room->id . '/qrcodes/' . $marker_id . '.svg';
        Storage::disk('public')->put($qrPath, $qrImage);

        $room->update([
            'marker_id' => $marker_id,
            'qr_code_path' => $qrPath,
        ]);

        // Carousel images
        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $carouselImage) {
                $path = $carouselImage->store('rooms/' . $room->id . '/carousel', 'public');
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $path
                ]);
            }
        }

        session()->flash('success', "{$room->name} was added successfully.");

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('room.show', $room->id)], 200);
        }

        return redirect()->route('room.show', $room->id)
            ->with('success', "{$room->name} was added successfully.");
    }

    public function show(Room $room)
    {
        $room->load(['images' => function ($query) {
            $query->withTrashed(); // Include soft-deleted images
        }, 'staff']);

        $images = $room->images;

        $room->load('officeHours'); // eager load

        if (!$room->qr_code_path || !Storage::disk('public')->exists($room->qr_code_path)) {
            $marker_id = 'room_' . $room->id;
            $qrImage = QrCode::format('svg')->size(300)->generate($room->id);
            $qrPath = 'qrcodes/' . $marker_id . '.svg';
            Storage::disk('public')->put($qrPath, $qrImage);

            $room->update([
                'qr_code_path' => $qrPath,
            ]);
        }

        return view('pages.admin.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $staffs = Staff::all();
        $room->load(['images' => function ($query) {
            $query->withTrashed(); // Include soft-deleted images
        }]);

        // Transform officeHours relation to JSON structure expected by JS
        $officeHours = [];
        foreach ($room->officeHours as $hour) {
            $officeHours[$hour->day][] = [
                'start' => \Carbon\Carbon::parse($hour->start_time)->format('H:i'),
                'end'   => \Carbon\Carbon::parse($hour->end_time)->format('H:i'),
            ];
        }


        return view('pages.admin.rooms.edit', compact('room', 'staffs', 'officeHours'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_path' => 'nullable|image|max:51200',
            'video_path' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:102400',
            'carousel_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:102400',

            'office_hours' => 'nullable|array',
        ]);

        $roomData = collect($validated)->except('office_hours')->toArray();
        $room->update($roomData);

        // Remove old hours
        $room->officeHours()->delete();

        if ($request->has('office_hours')) {
            foreach ($request->office_hours as $day => $ranges) {
                foreach ($ranges as $range) {
                    if (!empty($range['start']) && !empty($range['end'])) {
                        $room->officeHours()->create([
                            'day'        => $day,
                            'start_time' => $range['start'],
                            'end_time'   => $range['end'],
                        ]);
                    }
                }
            }
        }

        // Handle main image removal
        if ($request->input('remove_image_path') && $room->image_path) {
            if (Storage::disk('public')->exists($room->image_path)) {
                Storage::disk('public')->delete($room->image_path);
            }
            $room->image_path = null;
            $room->save();
        }

        // Update cover image (permanently delete old one)
        if ($request->hasFile('image_path')) {
            // Permanently delete old cover image file
            if ($room->image_path && Storage::disk('public')->exists($room->image_path)) {
                Storage::disk('public')->delete($room->image_path);
            }

            // Store new cover image
            $newImagePath = $request->file('image_path')->store('rooms/' . $room->id . '/cover_images', 'public');
            $room->image_path = $newImagePath;
            $room->save();
        }

        // Handle video removal
        if ($request->input('remove_video_path') && $room->video_path) {
            if (Storage::disk('public')->exists($room->video_path)) {
                Storage::disk('public')->delete($room->video_path);
            }
            $room->video_path = null;
            $room->save();
        }

        // Update video (permanently delete old one)
        if ($request->hasFile('video_path')) {
            // Permanently delete old video file
            if ($room->video_path && Storage::disk('public')->exists($room->video_path)) {
                Storage::disk('public')->delete($room->video_path);
            }

            // Store new video
            $newVideoPath = $request->file('video_path')->store('rooms/' . $room->id . '/videos', 'public');
            $room->video_path = $newVideoPath;
            $room->save();
        }


        // Permanently delete selected carousel images (same as before)
        if ($request->filled('remove_images')) {
            $images = RoomImage::whereIn('id', $request->remove_images)->get();
            foreach ($images as $img) {
                // Permanently delete file from storage
                if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                    Storage::disk('public')->delete($img->image_path);
                }
                // Permanently delete database record
                $img->forceDelete();
            }
        }

        // Add new carousel images
        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $carouselImage) {
                $path = $carouselImage->store('rooms/' . $room->id . '/carousel', 'public');
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $path
                ]);
            }
        }

        $room->load(['images' => function ($query) {
            $query->withTrashed();
        }]);

        session()->flash('success', "{$room->name} was updated successfully.");

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('room.show', $room->id)], 200);
        }

        return redirect()->route('room.show', $room->id)
            ->with('success', "{$room->name} was updated successfully.");
    }

    public function destroy(Room $room)
    {
        // Soft delete the room (images are soft-deleted via cascade, video_path remains untouched)
        $room->delete();

        return redirect()->route('room.index')
            ->with('success', 'Room deleted successfully.');
    }

    public function recycleBin()
    {
        $rooms = Room::onlyTrashed()->get();
        $staffs = Staff::onlyTrashed()->get();

        return view('pages.admin.recycle-bin', compact('rooms', 'staffs'));
    }

    public function restore($id)
    {
        $room = Room::onlyTrashed()->findOrFail($id);
        $room->restore();

        // Restore soft-deleted carousel images
        RoomImage::onlyTrashed()->where('room_id', $room->id)->restore();

        // Regenerate QR code if missing
        if (!$room->qr_code_path || !Storage::disk('public')->exists($room->qr_code_path)) {
            $marker_id = 'room_' . $room->id;
            $qrImage = QrCode::format('svg')->size(300)->generate($room->id);
            $qrPath = 'rooms/' . $room->id . '/qrcodes/' . $marker_id . '.svg';
            Storage::disk('public')->put($qrPath, $qrImage);

            $room->update([
                'marker_id' => $marker_id,
                'qr_code_path' => $qrPath,
            ]);
        }

        return redirect()->route('room.recycle-bin')
            ->with('success', 'Room and associated images restored successfully.');
    }

    public function forceDelete($id)
    {
        $room = Room::onlyTrashed()->findOrFail($id);

        // Delete files
        foreach ([$room->image_path, $room->video_path, $room->qr_code_path] as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // Delete carousel images
        foreach ($room->images()->withTrashed()->get() as $image) {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->forceDelete();
        }

        $room->forceDelete();

        return redirect()->route('room.recycle-bin')
            ->with('success', 'Room permanently deleted.');
    }

    public function removeCarouselImage(RoomImage $image)
    {
        if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->forceDelete(); // Permanently delete

        return back()->with('success', 'Image removed successfully.');
    }

    public function printQRCode(Room $room)
    {
        return view('pages.admin.rooms.print-qrcode', compact('room'));
    }

    public function assign(Request $request, $roomId = null)
    {
        $rooms = Room::all();

        // Use route parameter first, then query parameter, then default
        $roomId = $roomId ?? $request->query('roomId') ?? ($rooms->first()->id ?? null);
        $selectedRoom = $roomId ? Room::find($roomId) : null;
        $staff = Staff::with('room')->get();

        // Paginate staff
        $staff = Staff::with('room')->paginate(12);

        return view('pages.admin.rooms.assign', compact('rooms', 'staff', 'selectedRoom'));
    }

    public function assignStaff(Request $request)
    {
        //For Debugging
        Log::info('Room ID received: ' . $request->room_id);
        Log::info('Current page: ' . $request->input('page', 1));

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:staff,id',
        ]);

        $roomId = $request->room_id;
        $staffIds = $request->staff_ids ?? [];

        // Get currently assigned staff
        $currentlyAssigned = Staff::where('room_id', $roomId)->pluck('id')->toArray();

        // Determine who to unassign and assign
        $toUnassign = array_diff($currentlyAssigned, $staffIds);
        $toAssign = array_diff($staffIds, $currentlyAssigned);

        if (!empty($toUnassign)) {
            Staff::whereIn('id', $toUnassign)->update(['room_id' => null]);
        }
        if (!empty($toAssign)) {
            Staff::whereIn('id', $toAssign)->update(['room_id' => $roomId]);
        }

        // Re-fetch the correct room by ID to guarantee we have the name
        $room = Room::findOrFail($roomId);

        // Get all staff models that were just assigned
        // Eloquent won’t let you pluck('full_name') because it’s an accessor, not a real DB column.
        // So instead, fetch and map.
        // That way it uses your accessor properly.
        $assignedStaff = Staff::whereIn('id', $toAssign)
            ->get()
            ->map(fn($s) => $s->full_name)
            ->toArray();

        // Build message
        $staffNames = !empty($assignedStaff) ? implode(', ', $assignedStaff) : 'No staff';

        // Get current page from request
        $page = $request->input('page', 1);

        //For Debugging
        Log::info('Redirecting to room: ' . $request->room_id . ' with page: ' . $page);

        // Redirect with both roomId and page parameters
        return redirect()
            ->route('room.assign', ['roomId' => $room->id])
            ->with('success', "{$staffNames} was successfully assigned to {$room->name}.")
            ->withInput(['roomId' => $room->id, 'page' => $page]);
    }

    public function removeFromRoom(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $room = $staff->room; // assumes Staff has a belongsTo(Room::class) relationship

        $name = $staff->full_name;

        $staff->room_id = null;
        $staff->save();

        // Preserve pagination page after the page reload
        // Get page from request or session
        $page = $request->input('page') ?? session('current_page', 1);

        Log::info('Removing staff, current page: ' . $page);

        return redirect()
            ->route('room.assign', ['roomId' => $room->id])
            ->with('success', "{$name} was successfully removed from {$room->name}.")
            ->withInput(['roomId' => $room->id, 'page' => $page]);
    }
}
