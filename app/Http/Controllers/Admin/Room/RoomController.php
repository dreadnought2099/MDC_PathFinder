<?php

namespace App\Http\Controllers\Admin\Room;

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
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

/**
 * Room Management Controller
 * 
 * IMAGE PROCESSING PIPELINE (NOW MATCHES STAFF/PATH PATTERN):
 * 1. Frontend: Compresses to 2000px before upload
 * 2. Laravel: Validates 5MB + 3000px dimension limit (safety net)
 * 3. GD Check: Additional 3000px safety check (non-Imagick only)
 * 4. Backend: Final resize to 2000px + WebP conversion
 * 5. Error Handling: Graceful failures with logging
 */
class RoomController extends Controller
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin'])->only([
            'create',
            'store',
            'destroy',
            'restore',
            'forceDelete',
        ]);

        // Use Imagick if available, fallback to GD
        if (extension_loaded('imagick')) {
            $this->manager = new ImageManager(new ImagickDriver());
        } else {
            Log::warning('Imagick not available in RoomController, using GD driver. Install Imagick for better performance.');
            $this->manager = new ImageManager(new GdDriver());
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Retrieve inputs with session fallback
        $sort = $request->input('sort', session('rooms.sort', 'name'));
        $direction = strtolower($request->input('direction', session('rooms.direction', 'asc'))) === 'desc' ? 'desc' : 'asc';
        $search = trim($request->input('search', session('rooms.search', '')));

        // Store in session
        session([
            'rooms.sort' => $sort,
            'rooms.direction' => $direction,
            'rooms.search' => $search,
        ]);

        // Build query using new scopes
        $query = Room::withCount('images')
            ->when($user->hasRole('Office Manager'), fn($q) => $q->where('id', $user->room_id))
            ->search($search)
            ->sortBy($sort, $direction);

        // Role-based data access
        if ($user->hasRole('Admin')) {
            $rooms = $query->paginate(10)->appends([
                'sort' => $sort,
                'direction' => $direction,
                'search' => $search,
            ]);
        } else {
            $rooms = $query->get();
        }

        // AJAX partial rendering
        if ($request->ajax()) {
            return response()->json([
                'html' => view('pages.admin.rooms.partials.room-table', compact('rooms'))->render(),
            ]);
        }

        // Render full view
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
            // FIXED: Added dimension validation
            'image_path' => [
                'nullable',
                'image',
                'max:5120', // 5MB (reduced from 10MB)
                'dimensions:max_width=3000,max_height=3000' // NEW: Safety limit
            ],
            'video_path' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:51200',
            'carousel_images' => 'nullable|array|max:15',
            // FIXED: Added dimension validation
            'carousel_images.*' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120', // 5MB (reduced from 10MB)
                'dimensions:max_width=3000,max_height=3000' // NEW: Safety limit
            ],
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

            // FIXED: Process cover image with proper error handling
            if ($request->hasFile('image_path')) {
                try {
                    $webpPath = $this->convertToWebP(
                        $request->file('image_path'),
                        "offices/{$room->id}/cover_images"
                    );
                    $room->image_path = $webpPath;
                } catch (\Exception $e) {
                    Log::error('Cover image conversion failed', [
                        'room_id' => $room->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail entire operation, just skip image
                    DB::rollBack();
                    return back()->withInput()->withErrors([
                        'image_path' => 'Image processing failed. Please try a smaller image.'
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

            // FIXED: Process carousel images with proper error handling
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
                        gc_collect_cycles(); // NEW: Memory cleanup
                    } catch (\Exception $e) {
                        $failedCount++;
                        Log::error('Carousel image conversion failed', [
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
        // Authorization check (important!)
        $this->authorize('view', $room);

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
            // FIXED: Added dimension validation
            'image_path' => [
                'nullable',
                'image',
                'max:5120',
                'dimensions:max_width=3000,max_height=3000'
            ],
            'video_path' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:51200',
            'carousel_images' => 'nullable|array|max:15',
            // FIXED: Added dimension validation
            'carousel_images.*' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
                'dimensions:max_width=3000,max_height=3000'
            ],
            'office_hours' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $oldType = $room->room_type;

            // Store old paths BEFORE updating with validated data
            $oldImagePath = $room->image_path;
            $oldVideoPath = $room->video_path;

            // Update room with data EXCLUDING files (we'll handle them separately)
            $roomData = collect($validated)
                ->except(['office_hours', 'image_path', 'video_path', 'carousel_images'])
                ->toArray();
            $room->update($roomData);

            // -----------------------------
            // Handle Room Type Changes
            // -----------------------------
            if ($oldType !== $room->room_type) {
                // Delete office hours ONLY if changing TO entrance_point
                if ($room->room_type === 'entrance_point') {
                    $room->officeHours()->delete();
                }

                // Delete paths
                Path::where('from_room_id', $room->id)->forceDelete();
                Path::where('to_room_id', $room->id)->forceDelete();

                // Delete media if changing to entrance_point
                if ($room->room_type === 'entrance_point') {
                    if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                        $room->image_path = null;
                    }
                    if ($oldVideoPath && Storage::disk('public')->exists($oldVideoPath)) {
                        Storage::disk('public')->delete($oldVideoPath);
                        $room->video_path = null;
                    }

                    $carouselImages = $room->images()->withTrashed()->get();
                    foreach ($carouselImages as $img) {
                        if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                            Storage::disk('public')->delete($img->image_path);
                        }
                        $img->forceDelete();
                    }
                    $room->save();
                }

                $entranceService = app(EntrancePointService::class);
                if ($room->room_type === 'entrance_point') {
                    $entranceService->reconnectEntrancePoint($room);
                } else {
                    $entranceService->connectNewRoomToAllRooms($room);
                }
            }

            // -----------------------------
            // Handle Office Hours
            // -----------------------------
            if ($oldType !== $room->room_type && $room->room_type === 'entrance_point') {
                // Already deleted above
            } else {
                // Always clear old office hours first
                $room->officeHours()->delete();

                // Reinsert new ones only if provided
                if ($request->filled('office_hours')) {
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
            }

            // -----------------------------
            // Handle Cover Image removal
            // -----------------------------
            if ($request->input('remove_image_path') && $oldImagePath) {
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
                $room->image_path = null;
            }

            // -----------------------------
            // Handle Video removal
            // -----------------------------
            if ($request->input('remove_video_path') && $oldVideoPath) {
                if (Storage::disk('public')->exists($oldVideoPath)) {
                    Storage::disk('public')->delete($oldVideoPath);
                }
                $room->video_path = null;
            }

            // -----------------------------
            // FIXED: Handle Cover Image upload with error handling
            // -----------------------------
            if ($request->hasFile('image_path')) {
                // Delete old image using the stored path
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }

                try {
                    $room->image_path = $this->convertToWebP(
                        $request->file('image_path'),
                        "offices/{$room->id}/cover_images"
                    );
                } catch (\Exception $e) {
                    Log::error('Cover image conversion failed', [
                        'room_id' => $room->id,
                        'error' => $e->getMessage()
                    ]);
                    DB::rollBack();
                    return back()->withInput()->withErrors([
                        'image_path' => 'Image processing failed. Please try a smaller image.'
                    ]);
                }
            }

            // -----------------------------
            // Handle Video upload
            // -----------------------------
            if ($request->hasFile('video_path')) {
                // Delete old video using the stored path
                if ($oldVideoPath && Storage::disk('public')->exists($oldVideoPath)) {
                    Storage::disk('public')->delete($oldVideoPath);
                }

                $room->video_path = $request->file('video_path')->store(
                    "offices/{$room->id}/videos",
                    'public'
                );
            }

            // -----------------------------
            // Save once after all updates
            // -----------------------------
            $room->save();

            // -----------------------------
            // Handle Carousel Images
            // -----------------------------
            if ($request->filled('remove_images')) {
                $images = RoomImage::whereIn('id', $request->remove_images)->get();
                foreach ($images as $img) {
                    if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                        Storage::disk('public')->delete($img->image_path);
                    }
                    $img->forceDelete();
                }
            }

            // FIXED: Handle carousel uploads with error handling
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
                        gc_collect_cycles(); // NEW: Memory cleanup
                    } catch (\Exception $e) {
                        $failedCount++;
                        Log::error('Carousel image conversion failed', [
                            'room_id' => $room->id,
                            'file' => $carouselImage->getClientOriginalName(),
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                if ($failedCount > 0) {
                    session()->flash('warning', "{$uploadedCount} carousel image(s) uploaded successfully, but {$failedCount} failed.");
                }
            }

            DB::commit();

            $room->load(['images' => fn($q) => $q->withTrashed()]);

            session()->flash('success', "{$room->name} was updated successfully.");

            return $request->expectsJson()
                ? response()->json(['redirect' => route('room.show', $room->id)], 200)
                : redirect()->route('room.show', $room->id)->with('success', "{$room->name} was updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Office update error: ' . $e->getMessage(), [
                'room_id' => $room->id,
                'request' => $request->except(['image_path', 'video_path', 'carousel_images'])
            ]);

            return back()->withInput()->with('error', 'Failed to update office: ' . $e->getMessage());
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
            ->with('success', 'Office deleted successfully.');
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
            ->with('success', 'Office and paths restored successfully, including connections to new offices.');
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
            ->with('success', 'Office permanently deleted.');
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
     * Convert uploaded image to WebP format with optimization
     * 
     * PROCESSING FLOW (NOW MATCHES STAFF/PATH):
     * ┌─────────────────────────────────────────────────────────────────┐
     * │ Example: User uploads 2800x2100px, 4.5MB image                  │
     * ├─────────────────────────────────────────────────────────────────┤
     * │ 1. Frontend Compression (compressImageCanvas)                   │
     * │    - Input: 2800x2100px                                         │
     * │    - Output: 2000x1500px, ~1.5MB JPEG                           │
     * ├─────────────────────────────────────────────────────────────────┤
     * │ 2. Laravel Validation ✓                                         │
     * │    - Size: 1.5MB < 5MB ✓                                        │
     * │    - Dimensions: 2000x1500 < 3000x3000 ✓                        │
     * ├─────────────────────────────────────────────────────────────────┤
     * │ 3. GD Dimension Check (if needed)                               │
     * │    - 2000x1500 < 3000px → No exception                          │
     * ├─────────────────────────────────────────────────────────────────┤
     * │ 4. Backend Processing (this method)                             │
     * │    - scaleDown(2000) → Already 2000px, no resize                │
     * │    - Convert to WebP @ 85% quality                              │
     * │    - Output: ~700KB WebP                                        │
     * │    - Saved to storage/app/public/offices/{id}/xxx.webp          │
     * └─────────────────────────────────────────────────────────────────┘
     * 
     * EDGE CASES:
     * - Direct API upload (5000x4000px): Rejected by Laravel validation
     * - Corrupted image: Caught by try-catch, logged, returns error
     * - GD without Imagick (3500px image): Pre-checked at line 350-358
     * 
     * DRIVER BEHAVIOR:
     * - Imagick: Handles any dimension efficiently, no pre-check needed
     * - GD: Requires 3000px pre-check to prevent memory exhaustion
     * 
     * @param \Illuminate\Http\UploadedFile $file Image file to process
     * @param string $folder Storage path (e.g., "offices/123/cover_images")
     * @return string Relative path to saved WebP file
     * @throws \Exception If GD encounters >3000px image or processing fails
     */
    private function convertToWebP($file, $folder)
    {
        // GD driver safety check (Imagick handles large images efficiently)
        if (!extension_loaded('imagick')) {
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo) {
                [$width, $height] = $imageInfo;
                $maxDimension = 3000;

                if ($width > $maxDimension || $height > $maxDimension) {
                    throw new \Exception(
                        "Image dimensions too large. Maximum {$maxDimension}px on either side when using GD driver."
                    );
                }
            }
        }

        $baseName = uniqid('', true);
        $webpPath = "{$folder}/{$baseName}.webp";

        // Process: Load → Resize → Convert → Save
        $image = $this->manager->read($file);

        // NEW: Resize to 2000px max (matches frontend compression target)
        $image->scaleDown(width: 2000);

        // Convert to WebP with high quality (frontend already compressed)
        $encodedImage = $image->toWebp(85);

        Storage::disk('public')->put($webpPath, (string) $encodedImage);

        // Clean up memory
        unset($image, $encodedImage);

        return $webpPath;
    }
}
