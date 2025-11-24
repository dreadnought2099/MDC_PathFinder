<?php

namespace App\Http\Controllers\Admin\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

/**
 * Staff Management Controller
 * 
 * Handles CRUD operations for staff members with advanced image processing.
 * 
 * IMAGE PROCESSING PIPELINE:
 * 1. Frontend: Compresses to 1200px before upload
 * 2. Laravel: Validates 5MB + 4000px dimension limit
 * 3. GD Check: Additional 4000px safety check (non-Imagick only)
 * 4. Backend: Final resize to 1200px + WebP conversion
 * 5. Error Handling: Graceful failures with logging
 */
class StaffController extends Controller
{
    private ImageManager $manager;

    /**
     * Initialize image processing driver
     * 
     * Automatically selects Imagick (preferred) or GD (fallback).
     * Check logs for "Imagick not available" warning if using GD.
     */
    public function __construct()
    {
        if (extension_loaded('imagick')) {
            $this->manager = new ImageManager(new ImagickDriver());
        } else {
            Log::warning('Imagick not available, using GD driver. Install Imagick for better performance.');
            $this->manager = new ImageManager(new GdDriver());
        }
    }

    public function index(Request $request)
    {
        $sort = $request->input('sort', session('staff.sort', 'full_name'));
        $direction = $request->input('direction', session('staff.direction', 'asc'));
        $search = $request->input('search', session('staff.search', ''));

        session([
            'staff.sort' => $sort,
            'staff.direction' => $direction,
            'staff.search' => $search,
        ]);

        $user = auth()->user();

        $query = Staff::with('room')
            ->when(
                $user->hasRole('Office Manager') && $user->room_id,
                fn($q) => $q->where('room_id', $user->room_id)
            )
            ->when(
                $user->hasRole('Office Manager') && !$user->room_id,
                fn($q) => $q->whereRaw('1 = 0')
            )
            ->search($search)
            ->sortBy($sort, $direction);

        $staffs = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('pages.admin.staffs.partials.staff-table', compact('staffs'))->render(),
            ]);
        }

        return view('pages.admin.staffs.index', compact('staffs', 'sort', 'direction', 'search'));
    }

    public function create()
    {
        $this->authorize('create', Staff::class);

        return view('pages.admin.staffs.create');
    }

    /**
     * Store new staff member with photo processing
     * 
     * VALIDATION:
     * - Max 5MB file size
     * - Max 4000x4000px dimensions (safety for direct uploads/API calls)
     * - Frontend typically compresses to 1200px before reaching here
     * 
     * ERROR HANDLING:
     * - Image processing failures are logged and return user-friendly errors
     * - Staff record created even if photo fails (photo_path remains null)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Staff::class);

        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'credentials' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'photo_path' => [
                'nullable',
                'image',
                'max:5120',
                'dimensions:max_width=4000,max_height=4000'
            ],
        ]);

        // Auto-assign room_id for Office Managers
        if (auth()->user()->hasRole('Office Manager') && auth()->user()->room_id) {
            $validated['room_id'] = auth()->user()->room_id;
        }

        $staff = Staff::create($validated);

        if ($request->hasFile('photo_path')) {
            try {
                $webpPath = $this->convertToWebP($request->file('photo_path'), "staffs/{$staff->id}");
                $staff->update(['photo_path' => $webpPath]);
            } catch (\Exception $e) {
                Log::error('Image conversion failed', [
                    'error' => $e->getMessage(),
                    'staff_id' => $staff->id
                ]);
                return back()->withErrors([
                    'photo_path' => 'Image processing failed. Please try a smaller image.'
                ]);
            }
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
        $this->authorize('view', $staff);

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
        $this->authorize('update', $staff);
        return view('pages.admin.staffs.edit', compact('staff', 'rooms'));
    }

    /**
     * Update staff member with photo management
     * 
     * PHOTO HANDLING:
     * - Old photo automatically deleted when uploading new one
     * - Explicit deletion via 'delete_photo' flag
     * - All images converted to WebP for consistency
     */
    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $this->authorize('update', $staff);

        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'credentials' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'photo_path' => [
                'nullable',
                'image',
                'max:5120',
                'dimensions:max_width=4000,max_height=4000'
            ],
            'delete_photo' => 'nullable|string',
        ]);

        if ($request->delete_photo === "1" && $staff->photo_path) {
            if (Storage::disk('public')->exists($staff->photo_path)) {
                Storage::disk('public')->delete($staff->photo_path);
            }
            $staff->photo_path = null;
        }

        if ($request->hasFile('photo_path')) {
            if ($staff->photo_path && Storage::disk('public')->exists($staff->photo_path)) {
                Storage::disk('public')->delete($staff->photo_path);
            }

            try {
                $webpPath = $this->convertToWebP($request->file('photo_path'), "staffs/{$staff->id}");
                $validated['photo_path'] = $webpPath;
            } catch (\Exception $e) {
                Log::error('Image conversion failed', [
                    'error' => $e->getMessage(),
                    'staff_id' => $staff->id
                ]);
                return back()->withErrors([
                    'photo_path' => 'Image processing failed. Please try a smaller image.'
                ]);
            }
        }

        $staff->update($validated);

        session()->flash('success', "{$staff->full_name} updated successfully.");

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('staff.show', $staff->id)], 200);
        }

        return redirect()->route('staff.index')
            ->with('success', "{$staff->full_name} updated successfully.");
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $this->authorize('delete', $staff);
        $staff->delete();
        return redirect()->route('staff.index')
            ->with('success', "{$staff->full_name} moved to recycle bin");
    }

    public function restore($id)
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);

        // Authorization: Admin can restore any, Office Manager only their office's staff
        if (auth()->user()->hasRole('Office Manager')) {
            if (!auth()->user()->room_id || auth()->user()->room_id !== $staff->room_id) {
                abort(403, 'You can only restore staff from your office.');
            }
        }

        // Add this authorization check
        $this->authorize('restore', $staff);
        
        $staff->restore();

        // Get the tab parameter to maintain tab state
        $tab = request()->input('tab', 'staff');

        return redirect()->route('recycle-bin', ['tab' => $tab])
            ->with('success', "{$staff->full_name} restored successfully.");
    }

    public function forceDelete($id)
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);


        // Authorization: Admin can delete any, Office Manager only their office's staff
        if (auth()->user()->hasRole('Office Manager')) {
            if (!auth()->user()->room_id || auth()->user()->room_id !== $staff->room_id) {
                abort(403, 'You can only permanently delete staff from your office.');
            }
        }

        $this->authorize('delete', $staff);

        if (Storage::disk('public')->exists('staffs/' . $staff->id)) {
            Storage::disk('public')->deleteDirectory('staffs/' . $staff->id);
        }

        $staff->forceDelete();

        // Get the tab parameter to maintain tab state
        $tab = request()->input('tab', 'staff');

        return redirect()->route('recycle-bin', ['tab' => $tab])
            ->with('success', "{$staff->full_name} permanently deleted.");
    }

    public function search(Request $request)
    {
        $query = trim($request->query('q', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $staffs = Staff::with('room:id,name,room_type') // Add room_type to the select
            ->select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'room_id', 'full_name')
            ->when(strlen($query) >= 3, function ($q) use ($query) {
                $q->whereRaw("MATCH(first_name, middle_name, last_name, suffix, full_name) AGAINST(? IN BOOLEAN MODE)", [$query])
                    ->orWhere('first_name', 'like', "%{$query}%")
                    ->orWhere('middle_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('suffix', 'like', "%{$query}%")
                    ->orWhere('full_name', 'like', "%{$query}%");
            }, function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('middle_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('suffix', 'like', "%{$query}%")
                    ->orWhere('full_name', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        $results = $staffs->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->full_name,
            'room' => $s->room ? [
                'id' => $s->room->id,
                'name' => $s->room->name,
                'room_type' => $s->room->room_type // Add room_type to response
            ] : null,
        ]);

        return response()->json($results);
    }

    /**
     * Convert uploaded image to WebP format with optimization
     * 
     * PROCESSING FLOW:
     * ┌─────────────────────────────────────────────────────────────────┐
     * │ Example: User uploads 3500x2800px, 4.5MB image                  │
     * ├─────────────────────────────────────────────────────────────────┤
     * │ 1. Frontend Validation ✓                                        │
     * │    - Type: image/jpeg ✓                                         │
     * │    - Size: 4.5MB < 5MB ✓                                        │
     * ├─────────────────────────────────────────────────────────────────┤
     * │ 2. Frontend Compression (compressImageCanvas)                   │
     * │    - Input: 3500x2800px                                         │
     * │    - Output: 1200x960px, ~800KB JPEG                            │
     * ├─────────────────────────────────────────────────────────────────┤
     * │ 3. Laravel Validation ✓                                         │
     * │    - Type: image ✓                                              │
     * │    - Size: 800KB < 5MB ✓                                        │
     * │    - Dimensions: 1200x960 < 4000x4000 ✓                         │
     * ├─────────────────────────────────────────────────────────────────┤
     * │ 4. GD Dimension Check (skipped)                                 │
     * │    - 1200x960 < 4000px → No exception                           │
     * ├─────────────────────────────────────────────────────────────────┤
     * │ 5. Backend Processing (this method)                             │
     * │    - scaleDown(1200) → Already 1200px, no resize                │
     * │    - Convert to WebP @ 80% quality                              │
     * │    - Output: ~400KB WebP                                        │
     * │    - Saved to storage/app/public/staffs/{id}/xxx.webp           │
     * └─────────────────────────────────────────────────────────────────┘
     * 
     * EDGE CASES:
     * - Direct API upload (5000x4000px): Rejected by Laravel validation
     * - Corrupted image: Caught by try-catch, logged, returns error
     * - GD without Imagick (3500px image): Pre-checked at line 283-291
     * 
     * DRIVER BEHAVIOR:
     * - Imagick: Handles any dimension efficiently, no pre-check needed
     * - GD: Requires 4000px pre-check to prevent memory exhaustion
     * 
     * @param \Illuminate\Http\UploadedFile $file Image file to process
     * @param string $folder Storage path (e.g., "staffs/123")
     * @return string Relative path to saved WebP file
     * @throws \Exception If GD encounters >4000px image or processing fails
     */
    private function convertToWebP($file, $folder)
    {
        // GD driver safety check (Imagick handles large images efficiently)
        if (!extension_loaded('imagick')) {
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo) {
                [$width, $height] = $imageInfo;
                $maxDimension = 4000;

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
        $image->scaleDown(width: 1200);
        $encodedImage = $image->toWebp(80);

        Storage::disk('public')->put($webpPath, (string) $encodedImage);

        unset($image, $encodedImage);

        return $webpPath;
    }
}
