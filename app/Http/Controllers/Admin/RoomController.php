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
            'image_path' => 'nullable|image|max:5120',
            'video_path' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:51200',
            'office_hours' => 'nullable|string',
            'carousel_images.*' => 'nullable|image|max:5120',
        ]);

        $room = new Room($validated);

        // Save cover image
        if ($request->hasFile('image_path')) {
            $room->image_path = $request->file('image_path')->store('room_images', 'public');
        }

        // Save short video
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

        return redirect()->route('room.show', $room->id)
            ->with('success', "{$room->name} was added successfully.");
    }

    public function show(Room $room)
    {
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
            'image_path' => 'nullable|image|max:5120',
            'video_path' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:51200',
            'office_hours' => 'nullable|string',
            'carousel_images.*' => 'nullable|image|max:51200',
            'remove_images' => 'nullable|array', // IDs of carousel images to delete
        ]);

        $room->update($validated);

        // Update cover image
        if ($request->hasFile('image_path')) {
            if ($room->image_path) {
                Storage::disk('public')->delete($room->image_path);
            }
            $room->image_path = $request->file('image_path')->store('room_images', 'public');
        }

        // Update video
        if ($request->hasFile('video_path')) {
            if ($room->video_path) {
                Storage::disk('public')->delete($room->video_path);
            }
            $room->video_path = $request->file('video_path')->store('room_videos', 'public');
        }

        $room->save();

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

    // public function trashed()
    // {
    //     $rooms = Room::onlyTrashed()->get();
    //     return view('pages.admin.rooms.trashed', compact('rooms'));
    // }

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

        return redirect()->route('room.trashed')
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

        return redirect()->route('room.trashed')
            ->with('success', 'Room permanently deleted.');
    }

    public function removeCarouselImage(RoomImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Image removed successfully.');
    }
}
