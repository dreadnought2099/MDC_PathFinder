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
        ]);

        $room = new Room($validated);
        
        // Save cover image
        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('room_images', 'public');
            $room->image_path = 'storage/' . $imagePath;
        }

        // Save short video
        if ($request->hasFile('video_path')) {
            $videoPath = $request->file('video_path')->store('room_videos', 'public');
            $room->video_path = 'storage/' . $videoPath;
        }

        $room->save(); // Save to generate ID

        $marker_id = 'room_' . $room->id;
        
        // Generate QR code with just the room ID (better for scanning)
        $roomId = $room->id;

        $qrImage = QrCode::format('svg')
            ->size(300)
            ->generate($roomId);

        $qrPath = 'qrcodes/' . $marker_id . '.svg';
        Storage::disk('public')->put($qrPath, $qrImage);

        $room->update([
            'marker_id' => $marker_id,
            'qr_code_path' => 'storage/' . $qrPath,
        ]);


        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $carouselImage) {
                $path = $carouselImage->store('carousel_images/' . $room->id, 'public');

                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => 'storage/' . $path
                ]);
            }
        }

        return redirect()->route('room.show', $room->id);
    }

    public function show(Room $room)
    {
        $images = $room->images;
        
        // Regenerate QR code if it doesn't exist or is in old format
        if (!$room->qr_code_path || !Storage::disk('public')->exists(str_replace('storage/', '', $room->qr_code_path))) {
            $marker_id = 'room_' . $room->id;
            $roomId = $room->id;
            
            $qrImage = QrCode::format('svg')
                ->size(300)
                ->generate($roomId);
            
            $qrPath = 'qrcodes/' . $marker_id . '.svg';
            Storage::disk('public')->put($qrPath, $qrImage);
            
            $room->update([
                'qr_code_path' => 'storage/' . $qrPath,
            ]);
        }
        
        return view('pages.admin.rooms.show', compact('room', 'images'));
    }


    public function edit(Room $room)
    {
        $staffs = Staff::all();
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
        ]);

        // Update fields
        $room->fill($validated);

        // Replace cover image if uploaded
        if ($request->hasFile('image_path')) {
            if ($room->image_path) {
                Storage::disk('public')->delete(str_replace('storage/', '', $room->image_path));
            }
            $path = $request->file('image_path')->store('room_images', 'public');
            $room->image_path = 'storage/' . $path;
        }

        // Replace video if uploaded
        if ($request->hasFile('video_path')) {
            if ($room->video_path) {
                Storage::disk('public')->delete(str_replace('storage/', '', $room->video_path));
            }
            $path = $request->file('video_path')->store('room_videos', 'public');
            $room->video_path = 'storage/' . $path;
        }

        $room->save();

        return redirect()->route('room.show', $room->id)->with('success', 'Room updated successfully.');
    }


    public function destroy(Room $room)
    {
        // Delete cover image
        if ($room->image_path && Storage::disk('public')->exists(str_replace('storage/', '', $room->image_path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $room->image_path));
        }

        // Delete video
        if ($room->video_path && Storage::disk('public')->exists(str_replace('storage/', '', $room->video_path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $room->video_path));
        }

        // Delete QR code
        if ($room->qr_code_path && Storage::disk('public')->exists(str_replace('storage/', '', $room->qr_code_path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $room->qr_code_path));
        }

        // Delete carousel images
        foreach ($room->images as $image) {
            if ($image->image_path && Storage::disk('public')->exists(str_replace('storage/', '', $image->image_path))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $image->image_path));
            }
            $image->delete();
        }

        $room->delete();

        return redirect()->route('room.index')->with('success', 'Room deleted successfully.');
    }

    public function trashed() {
        
        $rooms = Room::onlyTrashed()->get();
        return view('pages.admin.rooms.trashed', compact('rooms'));
    }
}
