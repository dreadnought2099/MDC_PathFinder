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

class StaffController extends Controller
{
    private ImageManager $manager;

    public function __construct()
    {
        // Use Imagick if available, fallback to GD
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
        return view('pages.admin.staffs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'credentials' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255|unique:staff,email',
            'phone_num' => 'nullable|string|max:20',
            'photo_path' => [
                'nullable',
                'image',
                'max:5120',
                'dimensions:max_width=4000,max_height=4000' // Image Dimension validation
            ],
        ]);

        $staff = Staff::create($validated);

        if ($request->hasFile('photo_path')) {
            try {
                $webpPath = $this->convertToWebP($request->file('photo_path'), "staffs/{$staff->id}");
                $staff->update(['photo_path' => $webpPath]);
            } catch (\Exception $e) {
                Log::error('Image conversion failed', ['error' => $e->getMessage(), 'staff_id' => $staff->id]);
                return back()->withErrors(['photo_path' => 'Image processing failed. Please try a smaller image.']);
            }
        }

        session()->flash('success', "{$staff->full_name} was added successfully.");

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('staff.show', $staff->id)], 200);
        }

        return redirect()->route('staff.index')->with('success', "{$staff->full_name} was added successfully.");
    }

    public function show(Staff $staff)
    {
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
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('staff', 'email')->ignore($staff->id),
            ],
            'phone_num' => 'nullable|string|max:20',
            'photo_path' => [
                'nullable',
                'image',
                'max:5120',
                'dimensions:max_width=4000,max_height=4000' // Image Dimension validation
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
                Log::error('Image conversion failed', ['error' => $e->getMessage(), 'staff_id' => $staff->id]);
                return back()->withErrors(['photo_path' => 'Image processing failed. Please try a smaller image.']);
            }
        }
        
        $staff->update($validated);

        session()->flash('success', "{$staff->full_name} updated successfully.");

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('staff.show', $staff->id)], 200);
        }

        return redirect()->route('staff.index')->with('success', "{$staff->full_name} updated successfully.");
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();
        return redirect()->route('staff.index')->with('success', "{$staff->full_name} moved to recycle bin");
    }

    public function restore($id)
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);
        $staff->restore();
        return redirect()->route('recycle-bin')->with('success', "{$staff->full_name} restored successfully.");
    }

    public function forceDelete($id)
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);

        if (Storage::disk('public')->exists('staffs/' . $staff->id)) {
            Storage::disk('public')->deleteDirectory('staffs/' . $staff->id);
        }

        $staff->forceDelete();

        return redirect()->route('recycle-bin')->with('success', "{$staff->full_name} permanently deleted.");
    }

    public function checkEmail(Request $request)
    {
        $exists = Staff::where('email', $request->query('email'))->exists();
        return response()->json(['exists' => $exists]);
    }

    public function search(Request $request)
    {
        $query = trim($request->query('q', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $staffs = Staff::with('room')
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
            'room' => $s->room ? ['id' => $s->room->id, 'name' => $s->room->name] : null,
        ]);

        return response()->json($results);
    }

    /**
     * Convert uploaded image to WebP format
     * Uses Imagick if available, falls back to GD
     * Intervention Image v3 syntax
     */
    private function convertToWebP($file, $folder)
    {
        // Check dimensions BEFORE processing if using GD
        if (!extension_loaded('imagick')) {
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo) {
                [$width, $height] = $imageInfo;
                $maxDimension = 4000; // Adjust based on your server's memory_limit

                if ($width > $maxDimension || $height > $maxDimension) {
                    throw new \Exception("Image dimensions too large. Maximum {$maxDimension}px on either side when using GD driver.");
                }
            }
        }

        $baseName = uniqid('', true);
        $webpPath = "{$folder}/{$baseName}.webp";

        // Read image using Intervention Image v3
        $image = $this->manager->read($file);

        // Resize to max 1200px width to prevent memory issues
        // Works with both Imagick and GD drivers
        $image->scaleDown(width: 1200);

        // Convert to WebP with 80% quality
        $encodedImage = $image->toWebp(80);

        // Save to storage
        Storage::disk('public')->put($webpPath, (string) $encodedImage);

        // Memory is automatically managed in v3
        unset($image, $encodedImage);

        return $webpPath;
    }
}
