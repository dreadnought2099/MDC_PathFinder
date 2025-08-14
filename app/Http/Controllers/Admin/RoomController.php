<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
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
            'office_days' => 'nullable|array',
            'office_days.*' => 'string|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'office_hours_start' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'office_hours_end' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', 'after:office_hours_start'],
            'carousel_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:102400',
        ]);

        if (!empty($validated['office_hours_start'])) {
            $validated['office_hours_start'] = date('H:i', strtotime($validated['office_hours_start']));
        }
        if (!empty($validated['office_hours_end'])) {
            $validated['office_hours_end'] = date('H:i', strtotime($validated['office_hours_end']));
        }

        if (!empty($validated['office_hours_start']) && !empty($validated['office_hours_end'])) {
            if (strtotime($validated['office_hours_end']) <= strtotime($validated['office_hours_start'])) {
                return back()->withErrors(['office_hours_end' => 'End time must be after start time'])->withInput();
            }
        }

        $room = new Room($validated);

        $room->office_days = !empty($request->office_days) ? implode(',', $request->office_days) : null;
        $room->office_hours_start = $validated['office_hours_start'] ?? null;
        $room->office_hours_end = $validated['office_hours_end'] ?? null;

        if ($request->hasFile('image_path')) {
            $room->image_path = $request->file('image_path')->store('room_images', 'public');
        }

        if ($request->hasFile('video_path')) {
            $room->video_path = $request->file('video_path')->store('room_videos', 'public');
        }

        $room->save();

        $marker_id = 'room_' . $room->id;
        $qrImage = QrCode::format('svg')->size(300)->generate($room->id);
        $qrPath = 'qrcodes/' . $marker_id . '.svg';
        Storage::disk('public')->put($qrPath, $qrImage);

        $room->update([
            'marker_id' => $marker_id,
            'qr_code_path' => $qrPath,
        ]);

        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $carouselImage) {
                $path = $carouselImage->store('carousel_images/' . $room->id, 'public');
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
        return view('pages.admin.rooms.edit', compact('room', 'staffs'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_path' => 'nullable|image|max:51200',
            'video_path' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:102400',
            'office_days' => 'nullable|array',
            'office_days.*' => 'string|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'office_hours_start' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'office_hours_end' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', 'after:office_hours_start'],
            'carousel_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:102400',
            'remove_images' => 'nullable|array',
            'remove_image_path' => 'nullable|boolean',
            'remove_video_path' => 'nullable|boolean',
        ]);

        if (!empty($validated['office_hours_start'])) {
            $validated['office_hours_start'] = date('H:i', strtotime($validated['office_hours_start']));
        }
        if (!empty($validated['office_hours_end'])) {
            $validated['office_hours_end'] = date('H:i', strtotime($validated['office_hours_end']));
        }

        if (!empty($validated['office_hours_start']) && !empty($validated['office_hours_end'])) {
            if (strtotime($validated['office_hours_end']) <= strtotime($validated['office_hours_start'])) {
                return back()->withErrors(['office_hours_end' => 'End time must be after start time'])->withInput();
            }
        }

        $room->office_days = !empty($request->office_days) ? implode(',', $request->office_days) : null;
        $room->office_hours_start = $validated['office_hours_start'] ?? null;
        $room->office_hours_end = $validated['office_hours_end'] ?? null;

        unset($validated['office_days'], $validated['office_hours_start'], $validated['office_hours_end']);
        $room->update($validated);

        // Handle main image removal
        if ($request->input('remove_image_path') && $room->image_path) {
            if (Storage::disk('public')->exists($room->image_path)) {
                Storage::disk('public')->delete($room->image_path);
            }
            $room->image_path = null;
            $room->save();
        }

        // Handle new main image upload
        if ($request->hasFile('image_path')) {
            $newPath = $request->file('image_path')->store('room_images', 'public');
            if ($newPath) {
                if ($room->image_path && Storage::disk('public')->exists($room->image_path)) {
                    Storage::disk('public')->delete($room->image_path);
                }
                $room->image_path = $newPath;
                $room->save();
            }
        }

        // Handle video removal
        if ($request->input('remove_video_path') && $room->video_path) {
            if (Storage::disk('public')->exists($room->video_path)) {
                Storage::disk('public')->delete($room->video_path);
            }
            $room->video_path = null;
            $room->save();
        }

        // Handle new video upload
        if ($request->hasFile('video_path')) {
            $newVideoPath = $request->file('video_path')->store('room_videos', 'public');
            if ($newVideoPath) {
                if ($room->video_path && Storage::disk('public')->exists($room->video_path)) {
                    Storage::disk('public')->delete($room->video_path);
                }
                $room->video_path = $newVideoPath;
                $room->save();
            }
        }

        // Permanently delete selected carousel images
        if ($request->filled('remove_images')) {
            $images = RoomImage::whereIn('id', $request->remove_images)->get();
            foreach ($images as $img) {
                if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                    Storage::disk('public')->delete($img->image_path);
                }
                $img->forceDelete(); // Permanently delete
            }
        }

        // Handle new carousel images
        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $carouselImage) {
                $path = $carouselImage->store('carousel_images/' . $room->id, 'public');
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
            $qrPath = 'qrcodes/' . $marker_id . '.svg';
            Storage::disk('public')->put($qrPath, $qrImage);

            $room->update([
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
        $roomId = $request->query('roomId') ?? ($rooms->first()->id ?? null);
        $selectedRoom = $roomId ? Room::find($roomId) : null;
        $staff = Staff::with('room')->get();

        return view('pages.admin.rooms.assign', compact('rooms', 'staff', 'selectedRoom'));
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

        if (!empty($toAssign) || !empty($toUnassign)) {
            return redirect()->route('room.assign', $roomId)
                ->with('success', 'Staff assignment updated successfully.');
        }

        return redirect()->route('room.assign', $roomId);
    }

    public function removeFromRoom($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->room_id = null;
        $staff->save();

        return response()->json([
            'success' => true,
            'message' => "{$staff->name} removed from room."
        ]);
    }
}