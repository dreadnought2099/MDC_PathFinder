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
            'marker_id' => 'required|string|max:255',
            'image_path' => 'nullable|string',
            'video_path' => 'nullable|string',
            'office_hours' => 'nullable|string',
        ]);

        $room = new Room($validated);
        $room->save();

        if ($request->hasFile('carousel_images')) {
            foreach ($request->file('carousel_images') as $image) {
                $path = $image->store('room_carousel', 'public');

                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $path,
                ]);
            }
        }


        // Generate QR code based on the room's ID or marker_id
        $qrImage = QrCode::format('png')->size(300)->generate($room->marker_id);
        $filePath = 'qrcodes/room_' . $room->id . '.png';

        // Save to public storage
        Storage::disk('public')->put($filePath, $qrImage);
        $room->qr_code_path = 'storage/' . $filePath;
        $room->save();

        return redirect()->route('room.show', $room->id);
    }
}
