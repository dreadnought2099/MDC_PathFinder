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
            'video_path' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:51200',
            'office_days' => 'nullable|array',
            'office_days.*' => 'string|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            // Allow HH:MM or HH:MM:SS
            'office_hours_start' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'office_hours_end'   => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', 'after:office_hours_start'],
            'carousel_images.*' => 'nullable|image|max:51200',
        ]);

        // Normalize to HH:MM for storage
        if (!empty($validated['office_hours_start'])) {
            $validated['office_hours_start'] = date('H:i', strtotime($validated['office_hours_start']));
        }
        if (!empty($validated['office_hours_end'])) {
            $validated['office_hours_end'] = date('H:i', strtotime($validated['office_hours_end']));
        }

        // Check end time after start time manually
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

        // Marker ID and QR code
        $marker_id = 'room_' . $room->id;
        $qrImage = QrCode::format('svg')->size(300)->generate($room->id);
        $qrPath = 'qrcodes/' . $marker_id . '.svg';
        Storage::disk('public')->put($qrPath, $qrImage);

        $room->update([
            'marker_id' => $marker_id,
            'qr_code_path' => $qrPath,
        ]);

        // Carousel images
        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $carouselImage) {
                $path = $carouselImage->store('carousel_images/' . $room->id, 'public');
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $path
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('room.show', $room->id)], 200);
        }

        return redirect()->route('room.show', $room->id)
            ->with('success', "{$room->name} was added successfully.");
    }


    public function show(Room $room)
    {
        $room->load('images');

        $images = $room->images;

        // Regenerate QR if missing
        if (!$room->qr_code_path || !Storage::disk('public')->exists($room->qr_code_path)) {
            $marker_id = 'room_' . $room->id;
            $qrImage = QrCode::format('svg')->size(300)->generate($room->id);
            $qrPath = 'qrcodes/' . $marker_id . '.svg';
            Storage::disk('public')->put($qrPath, $qrImage);

            $room->update([
                'qr_code_path' => $qrPath,
            ]);
        }

        return view('pages.admin.rooms.show', compact('room', 'images'));
    }

    public function edit(Room $room)
    {
        $staffs = Staff::all();
        $room->load('images');
        return view('pages.admin.rooms.edit', compact('room', 'staffs'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_path' => 'nullable|image|max:51200',
            'video_path' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:51200',
            'office_days' => 'nullable|array',
            'office_days.*' => 'string|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            // Allow HH:MM or HH:MM:SS
            'office_hours_start' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'office_hours_end'   => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', 'after:office_hours_start'],
            'carousel_images.*' => 'nullable|image|max:51200',
            'remove_images' => 'nullable|array',
        ]);

        // Normalize office hours
        if (!empty($validated['office_hours_start'])) {
            $validated['office_hours_start'] = date('H:i', strtotime($validated['office_hours_start']));
        }
        if (!empty($validated['office_hours_end'])) {
            $validated['office_hours_end'] = date('H:i', strtotime($validated['office_hours_end']));
        }

        // Check end time after start time manually
        if (!empty($validated['office_hours_start']) && !empty($validated['office_hours_end'])) {
            if (strtotime($validated['office_hours_end']) <= strtotime($validated['office_hours_start'])) {
                return back()->withErrors(['office_hours_end' => 'End time must be after start time'])->withInput();
            }
        }

        // Update fields
        $room->office_days = !empty($request->office_days) ? implode(',', $request->office_days) : null;
        $room->office_hours_start = $validated['office_hours_start'] ?? null;
        $room->office_hours_end = $validated['office_hours_end'] ?? null;

        unset($validated['office_days'], $validated['office_hours_start'], $validated['office_hours_end']);
        $room->update($validated);

        // Update cover image
        if ($request->hasFile('image_path')) {
            $newPath = $request->file('image_path')->store('room_images', 'public');
            if ($newPath) {
                if ($room->image_path) {
                    Storage::disk('public')->delete($room->image_path);
                }
                $room->image_path = $newPath;
                $room->save();
            }
        }

        // Update video
        if ($request->hasFile('video_path')) {
            $newVideoPath = $request->file('video_path')->store('room_videos', 'public');
            if ($newVideoPath) {
                if ($room->video_path) {
                    Storage::disk('public')->delete($room->video_path);
                }
                $room->video_path = $newVideoPath;
                $room->save();
            }
        }

        // Remove selected carousel images
        if ($request->filled('remove_images')) {
            $images = RoomImage::whereIn('id', $request->remove_images)->get();
            foreach ($images as $img) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }
        }

        // Add new carousel images
        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $carouselImage) {
                $path = $carouselImage->store('carousel_images/' . $room->id, 'public');
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $path
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('room.show', $room->id)], 200);
        }

        return redirect()->route('room.show', $room->id)
            ->with('success', "{$room->name} was updated successfully.");
    }

    public function destroy(Room $room)
    {
        // Delete files
        if ($room->image_path && Storage::disk('public')->exists($room->image_path)) {
            Storage::disk('public')->delete($room->image_path);
        }

        if ($room->video_path && Storage::disk('public')->exists($room->video_path)) {
            Storage::disk('public')->delete($room->video_path);
        }

        if ($room->qr_code_path && Storage::disk('public')->exists($room->qr_code_path)) {
            Storage::disk('public')->delete($room->qr_code_path);
        }

        foreach ($room->images as $image) {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

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

        return redirect()->route('room.recycle-bin')
            ->with('success', 'Room restored successfully.');
    }

    public function forceDelete($id)
    {
        $room = Room::onlyTrashed()->findOrFail($id);

        // Main files
        foreach ([$room->image_path, $room->video_path, $room->qr_code_path] as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // Carousel images
        foreach ($room->images as $image) {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        $room->forceDelete();

        return redirect()->route('room.recycle-bin')
            ->with('success', 'Room permanently deleted.');
    }

    public function removeCarouselImage(RoomImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Image removed successfully.');
    }

    public function printQRCode(Room $room)
    {
        return view('pages.admin.rooms.print-qrcode', compact('room'));
    }
}
