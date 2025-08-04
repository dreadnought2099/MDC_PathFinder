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

        $qrImage = QrCode::format('png')
            ->size(300)
            ->generate($marker_id);

        $qrPath = 'qrcodes/' . $marker_id . '.png';

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
}
