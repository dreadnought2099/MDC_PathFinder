<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\Staff;
use App\Services\EntrancePointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Drivers\Gd\Driver;

class RoomController extends Controller
{
    private $manager;

    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin'])->only([
            'create',
            'store',
            'destroy',
            'restore',
            'forceDelete',
            'recycleBin'
        ]);

        // Reduced memory since images are pre-compressed by frontend
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', '180');

        $this->manager = new ImageManager(new Driver());
    }

    public function index(Request $request)
    {
        // Get parameters (from request or session)
        $sort = $request->input('sort', session('rooms.sort', 'name'));
        $direction = $request->input('direction', session('rooms.direction', 'asc'));
        $search = $request->input('search', session('rooms.search', ''));

        // Always save to session
        session([
            'rooms.sort' => $sort,
            'rooms.direction' => $direction,
            'rooms.search' => $search,
        ]);

        // Build query
        $query = Room::query();

        // Apply search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Add image count if sorting by it
        if ($sort === 'images_count') {
            $query->withCount('images');
        }

        // Apply sorting
        $allowedSorts = ['id', 'name', 'description', 'room_type', 'images_count', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }

        // Paginate
        $rooms = $query->paginate(10)->withQueryString();

        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'html' => view('pages.admin.rooms.partials.room-table', compact('rooms'))->render(),
            ]);
        }

        return view('pages.admin.rooms.index', compact('rooms', 'sort', 'direction', 'search'));
    }

    public function create()
    {
        return view('pages.admin.rooms.create');
    }

    public function store(Request $request, EntrancePointService $entrancePointService)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms')->whereNull('deleted_at'),
            ],
            'description' => 'nullable|string',
            'room_type' => 'required|in:regular,entrance_point',
            'image_path' => 'nullable|image|max:10240',
            'video_path' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:51200',
            'carousel_images' => 'nullable|array|max:50',
            'carousel_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'office_hours' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $room = Room::create(collect($validated)->except('office_hours')->toArray());

            $connectionResult = $entrancePointService->connectNewRoomToAllRooms($room);

            $successMessage = "{$room->name} created and connected to {$connectionResult['rooms_connected']} office with {$connectionResult['paths_created']} paths.";

            // Save office hours
            if ($request->has('office_hours')) {
                foreach ($request->office_hours as $day => $ranges) {
                    foreach ($ranges as $range) {
                        if (!empty($range['start']) && !empty($range['end'])) {
                            $room->officeHours()->create([
                                'day' => $day,
                                'start_time' => $range['start'],
                                'end_time' => $range['end'],
                            ]);
                        }
                    }
                }
            }

            // Process cover image - Convert to WebP
            if ($request->hasFile('image_path')) {
                try {
                    $webpPath = $this->convertToWebP(
                        $request->file('image_path'),
                        "offices/{$room->id}/cover_images"
                    );
                    $room->image_path = $webpPath;
                } catch (\Exception $e) {
                    Log::error('Cover image WebP conversion failed', [
                        'room_id' => $room->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Process video
            if ($request->hasFile('video_path')) {
                try {
                    $path = $request->file('video_path')->store("offices/{$room->id}/videos", 'public');
                    $room->video_path = $path;
                } catch (\Exception $e) {
                    Log::error('Video upload failed', [
                        'room_id' => $room->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Generate QR code
            $marker_id = 'room_' . $room->id;
            $qrImage = QrCode::format('svg')->size(300)->generate($room->token);
            $qrPath = "offices/{$room->id}/qrcodes/{$marker_id}.svg";
            Storage::disk('public')->put($qrPath, $qrImage);

            $room->update([
                'marker_id' => $marker_id,
                'qr_code_path' => $qrPath,
            ]);

            // Process carousel images - Convert to WebP
            if ($request->hasFile('carousel_images')) {
                $uploadedCount = 0;
                $failedCount = 0;

                foreach ($request->file('carousel_images') as $carouselImage) {
                    try {
                        $webpPath = $this->convertToWebP(
                            $carouselImage,
                            "offices/{$room->id}/carousel"
                        );

                        RoomImage::create([
                            'room_id' => $room->id,
                            'image_path' => $webpPath
                        ]);

                        $uploadedCount++;
                    } catch (\Exception $e) {
                        $failedCount++;
                        Log::error('Carousel image WebP conversion failed', [
                            'room_id' => $room->id,
                            'file' => $carouselImage->getClientOriginalName(),
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                if ($failedCount > 0) {
                    $successMessage .= " However, {$failedCount} carousel image(s) failed to upload.";
                }
            }

            DB::commit();

            session()->flash('success', $successMessage);

            if ($request->expectsJson()) {
                return response()->json(['redirect' => route('room.show', $room->id)], 200);
            }

            return redirect()->route('room.show', $room->id)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Room creation error: ' . $e->getMessage(), [
                'request' => $request->except(['image_path', 'video_path', 'carousel_images'])
            ]);

            return back()->withInput()->with('error', 'Failed to create office: ' . $e->getMessage());
        }
    }

    public function show(Room $room)
    {
        $room->load([
            'images' => function ($query) {
                $query->withTrashed();
            },
            'staff',
            'officeHours'
        ]);

        if (!$room->qr_code_path || !Storage::disk('public')->exists($room->qr_code_path)) {
            $marker_id = 'room_' . $room->id;
            $qrImage = QrCode::format('svg')->size(300)->generate($room->token);
            $qrPath = "offices/{$room->id}/qrcodes/{$marker_id}.svg";
            Storage::disk('public')->put($qrPath, $qrImage);

            $room->update(['qr_code_path' => $qrPath]);
        }

        return view('pages.admin.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $this->authorize('update', $room);

        $staffs = Staff::all();
        $room->load(['images' => function ($query) {
            $query->withTrashed();
        }]);

        $officeHours = [];
        foreach ($room->officeHours as $hour) {
            $officeHours[$hour->day][] = [
                'start' => \Carbon\Carbon::parse($hour->start_time)->format('H:i'),
                'end' => \Carbon\Carbon::parse($hour->end_time)->format('H:i'),
            ];
        }

        $existingOfficeHours = $officeHours;

        return view('pages.admin.rooms.edit', compact('room', 'staffs', 'officeHours', 'existingOfficeHours'));
    }

    public function update(Request $request, Room $room)
    {
        $this->authorize('update', $room);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms')->ignore($room->id)->whereNull('deleted_at'),
            ],
            'description' => 'nullable|string',
            'room_type' => 'required|in:regular,entrance_point',
            'image_path' => 'nullable|image|max:10240',
            'video_path' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:51200',
            'carousel_images' => 'nullable|array|max:50',
            'carousel_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'office_hours' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $oldType = $room->room_type;
            $roomData = collect($validated)->except('office_hours')->toArray();
            $room->update($roomData);

            // Remove old office hours
            $room->officeHours()->delete();

            // Handle room type change
            if ($oldType !== $room->room_type) {
                Path::where('from_room_id', $room->id)
                    ->orWhere('to_room_id', $room->id)
                    ->delete();

                $entranceService = app(EntrancePointService::class);

                if ($room->room_type === 'entrance_point') {
                    $entranceService->reconnectEntrancePoint($room);
                } else {
                    $entranceService->connectNewRoomToAllRooms($room);
                }
            }

            // Save new office hours
            if ($request->has('office_hours')) {
                foreach ($request->office_hours as $day => $ranges) {
                    foreach ($ranges as $range) {
                        if (!empty($range['start']) && !empty($range['end'])) {
                            $room->officeHours()->create([
                                'day' => $day,
                                'start_time' => $range['start'],
                                'end_time' => $range['end'],
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
            }

            // Update cover image - Convert to WebP
            if ($request->hasFile('image_path')) {
                if ($room->image_path && Storage::disk('public')->exists($room->image_path)) {
                    Storage::disk('public')->delete($room->image_path);
                }

                try {
                    $webpPath = $this->convertToWebP(
                        $request->file('image_path'),
                        "offices/{$room->id}/cover_images"
                    );
                    $room->image_path = $webpPath;
                } catch (\Exception $e) {
                    Log::error('Cover image WebP conversion failed', [
                        'room_id' => $room->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            // Handle video removal
            if ($request->input('remove_video_path') && $room->video_path) {
                if (Storage::disk('public')->exists($room->video_path)) {
                    Storage::disk('public')->delete($room->video_path);
                }
                $room->video_path = null;
            }

            // Update video
            if ($request->hasFile('video_path')) {
                if ($room->video_path && Storage::disk('public')->exists($room->video_path)) {
                    Storage::disk('public')->delete($room->video_path);
                }

                $newVideoPath = $request->file('video_path')->store("offices/{$room->id}/videos", 'public');
                $room->video_path = $newVideoPath;
            }

            // Save any changes to room
            $room->save();

            // Delete selected carousel images
            if ($request->filled('remove_images')) {
                $images = RoomImage::whereIn('id', $request->remove_images)->get();
                foreach ($images as $img) {
                    if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                        Storage::disk('public')->delete($img->image_path);
                    }
                    $img->forceDelete();
                }
            }

            // Add new carousel images - Convert to WebP
            if ($request->hasFile('carousel_images')) {
                $uploadedCount = 0;
                $failedCount = 0;

                foreach ($request->file('carousel_images') as $carouselImage) {
                    try {
                        $webpPath = $this->convertToWebP(
                            $carouselImage,
                            "offices/{$room->id}/carousel"
                        );

                        RoomImage::create([
                            'room_id' => $room->id,
                            'image_path' => $webpPath
                        ]);

                        $uploadedCount++;
                    } catch (\Exception $e) {
                        $failedCount++;
                        Log::error('Carousel image WebP conversion failed', [
                            'room_id' => $room->id,
                            'file' => $carouselImage->getClientOriginalName(),
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            DB::commit();

            $room->load(['images' => function ($query) {
                $query->withTrashed();
            }]);

            $successMessage = "{$room->name} was updated successfully.";
            session()->flash('success', $successMessage);

            if ($request->expectsJson()) {
                return response()->json(['redirect' => route('room.show', $room->id)], 200);
            }

            return redirect()->route('room.show', $room->id)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Room update error: ' . $e->getMessage(), [
                'room_id' => $room->id,
                'request' => $request->except(['image_path', 'video_path', 'carousel_images'])
            ]);

            return back()->withInput()->with('error', 'Failed to update room: ' . $e->getMessage());
        }
    }

    public function destroy(Room $room, EntrancePointService $entrancePointService)
    {
        if ($room->room_type === 'entrance_point') {
            $entrancePointService->removeEntrancePointPaths($room);
        } else {
            Path::where('from_room_id', $room->id)
                ->orWhere('to_room_id', $room->id)
                ->delete();
        }

        $room->delete();

        return redirect()->route('room.index')
            ->with('success', 'Room deleted successfully.');
    }

    public function restore($id, EntrancePointService $entrancePointService)
    {
        $room = Room::onlyTrashed()->findOrFail($id);

        $room->restore();
        $room->refresh();

        RoomImage::onlyTrashed()->where('room_id', $room->id)->restore();

        Path::onlyTrashed()
            ->where('from_room_id', $room->id)
            ->orWhere('to_room_id', $room->id)
            ->restore();

        if (!$room->qr_code_path || !Storage::disk('public')->exists($room->qr_code_path)) {
            $marker_id = 'room_' . $room->id;
            $qrImage = QrCode::format('svg')->size(300)->generate($room->token);
            $qrPath = "offices/{$room->id}/qrcodes/{$marker_id}.svg";
            Storage::disk('public')->put($qrPath, $qrImage);

            $room->update([
                'marker_id' => $marker_id,
                'qr_code_path' => $qrPath,
            ]);
        }

        if ($room->room_type === 'entrance_point') {
            $entrancePointService->reconnectEntrancePoint($room);
        } else {
            $entrancePointService->connectNewRoomToAllRooms($room);
        }

        return redirect()->route('recycle-bin')
            ->with('success', 'Room and paths restored successfully, including connections to new rooms.');
    }

    public function forceDelete($id)
    {
        $room = Room::onlyTrashed()->findOrFail($id);

        foreach ([$room->image_path, $room->video_path, $room->qr_code_path] as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        foreach ($room->images()->withTrashed()->get() as $image) {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->forceDelete();
        }

        $room->forceDelete();

        return redirect()->route('recycle-bin')
            ->with('success', 'Room permanently deleted.');
    }

    public function removeCarouselImage(RoomImage $image)
    {
        if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->forceDelete();

        return back()->with('success', 'Image removed successfully.');
    }

    public function printQRCode(Room $room)
    {
        return view('pages.admin.rooms.print-qrcode', compact('room'));
    }

    public function checkName(Request $request)
    {
        $exists = Room::where('name', $request->name)->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Convert already-compressed image to WebP format
     * Frontend handles compression, backend handles format optimization
     */
    private function convertToWebP($file, $folder)
    {
        $baseName = uniqid('', true);
        $webpPath = "{$folder}/{$baseName}.webp";

        // Read the already-compressed image from frontend
        $image = $this->manager->read($file);

        // NO resizing - image is already properly sized by frontend
        // Just convert to WebP with high quality since it's pre-compressed
        $encodedImage = $image->encode(new WebpEncoder(quality: 90));

        // Clean up
        unset($image);

        // Store the WebP image
        Storage::disk('public')->put($webpPath, (string) $encodedImage);
        unset($encodedImage);

        return $webpPath;
    }
}
