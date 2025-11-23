<?php

namespace App\Http\Controllers\Admin\Path;

use App\Http\Controllers\Controller;
use App\Models\PathImage;
use App\Models\Path;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;

/**
 * Path Image Management Controller
 * 
 * Handles bulk image uploads for paths with advanced image processing.
 * 
 * IMAGE PROCESSING:
 * - Frontend: Compresses to 2000px before upload
 * - Laravel: Validates 5MB + 3000px dimension limit
 * - GD Safety: Additional 3000px check (non-Imagick only)
 * - Backend: Final resize to 2000px + WebP conversion
 * 
 * DIFFERENCES FROM STAFF:
 * - Larger dimensions (2000px vs 1200px) for path documentation
 * - Bulk upload support (up to 20 images)
 * - Lower WebP quality (70-75% vs 80%) due to larger images
 */
class PathImageController extends Controller
{
    private ImageManager $manager;

    // Configuration constant for per-path limit
    private const MAX_IMAGES_PER_PATH = 50;

    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin'])->only([
            'create',
            'store',
            'edit',
            'update',
            'updateSingle',
            'updateMultiple',
            'destroySingle',
            'destroyMultiple',
            'updateOrder'
        ]);

        $this->middleware('auth');

        // Use Imagick if available, fallback to GD
        if (extension_loaded('imagick')) {
            $this->manager = new ImageManager(new ImagickDriver());
        } else {
            Log::warning('Imagick not available in PathImageController, using GD driver. Install Imagick for better performance.');
            $this->manager = new ImageManager(new GdDriver());
        }
    }

    public function create(Path $path = null)
    {
        $paths = Path::with(['fromRoom', 'toRoom'])->get();

        if ($paths->isEmpty()) {
            return redirect()->route('path.index')
                ->with('warning', 'No paths available. Please create a path first.');
        }

        // Priority: URL parameter > session > first path
        if ($path) {
            $defaultPath = $path;
        } else {
            $selectedPathId = session('selectedPathId');
            $defaultPath = $selectedPathId ? Path::find($selectedPathId) : $paths->first();
        }

        // Always update session with the current path
        session(['selectedPathId' => $defaultPath->id]);

        // Get current image count for the default path
        $currentImageCount = PathImage::where('path_id', $defaultPath->id)->count();
        $maxImagesPerPath = self::MAX_IMAGES_PER_PATH;
        $remainingSlots = max(0, $maxImagesPerPath - $currentImageCount);

        return view('pages.admin.path_images.create', compact(
            'paths',
            'defaultPath',
            'currentImageCount',
            'maxImagesPerPath',
            'remainingSlots'
        ));
    }

    /**
     * Store multiple path images with validation
     * 
     * VALIDATION:
     * - Max 50 files per upload
     * - Max 10MB per file
     * - Max 3000x3000px dimensions (safety for direct uploads)
     * - Frontend typically compresses to 2000px before reaching here
     * - Added sort image by chronological order based by METADATA(Image datetime)
     * 
     * ERROR HANDLING:
     * - Individual file failures don't stop entire upload
     * - Errors logged and reported to user
     * - Partial success supported
     */
    public function store(Request $request)
    {
        if (!$request->hasFile('files') || count($request->file('files')) === 0) {
            return back()->withErrors(['files' => 'Please select at least one image file.']);
        }

        $request->validate([
            'path_id' => 'required|exists:paths,id',
            'files'   => 'required|array|min:1|max:50',
            'files.*' => [
                'required',
                'image',
                'max:10240', // 10 MB
                'dimensions:max_width=3000,max_height=3000'
            ],
        ]);

        $files = $request->file('files');

        try {
            // Use transaction with lock to prevent race conditions
            return DB::transaction(function () use ($request, $files) {
                // Lock the path row to prevent concurrent uploads
                $path = Path::lockForUpdate()->findOrFail($request->path_id);

                // Re-check count INSIDE transaction (critical!)
                $currentImageCount = PathImage::where('path_id', $path->id)->count();
                $newFileCount = count($files);
                $totalAfterUpload = $currentImageCount + $newFileCount;

                if ($totalAfterUpload > self::MAX_IMAGES_PER_PATH) {
                    $remaining = self::MAX_IMAGES_PER_PATH - $currentImageCount;
                    $errorMessage = $remaining > 0
                        ? "This path already has {$currentImageCount} images. Maximum allowed is " . self::MAX_IMAGES_PER_PATH . ". You can only upload {$remaining} more image(s)."
                        : "This path has reached the maximum limit of " . self::MAX_IMAGES_PER_PATH . " images.";

                    if ($request->ajax()) {
                        // Need to throw exception to rollback transaction
                        throw ValidationException::withMessages(['files' => $errorMessage]);
                    }

                    // For non-ajax, we'll handle this outside transaction
                    throw new \Exception($errorMessage);
                }

                $nextOrder = PathImage::where('path_id', $path->id)->max('image_order') ?? 0;

                // CHANGE: Extract EXIF data and sort files by datetime
                $filesWithMetadata = [];
                foreach ($files as $index => $file) {
                    $datetime = $this->extractImageDateTime($file);
                    $filesWithMetadata[] = [
                        'file' => $file,
                        'datetime' => $datetime,
                        'original_index' => $index, // Keep original order as fallback
                    ];
                }

                // Sort by datetime (oldest first), fallback to original order if no datetime
                usort($filesWithMetadata, function ($a, $b) {
                    // If both have datetime, sort by datetime
                    if ($a['datetime'] && $b['datetime']) {
                        return $a['datetime'] <=> $b['datetime'];
                    }
                    // If only one has datetime, prioritize it
                    if ($a['datetime']) return -1;
                    if ($b['datetime']) return 1;
                    // If neither has datetime, maintain original order
                    return $a['original_index'] <=> $b['original_index'];
                });

                $uploadedCount = 0;
                $errors = [];

                foreach ($filesWithMetadata as $fileData) {
                    $file = $fileData['file'];
                    try {
                        $webpPath = $this->processAndSaveImage($file, $path->id);

                        PathImage::create([
                            'path_id'     => $path->id,
                            'image_file'  => $webpPath,
                            'image_order' => ++$nextOrder,
                        ]);

                        $uploadedCount++;
                        gc_collect_cycles();
                    } catch (\Exception $e) {
                        $errors[] = "Failed to upload {$file->getClientOriginalName()}: " . $e->getMessage();

                        Log::error("PathImage upload failed", [
                            'path_id' => $path->id,
                            'file'    => $file->getClientOriginalName(),
                            'size'    => $file->getSize(),
                            'error'   => $e->getMessage(),
                        ]);
                    }
                }

                $message = "{$uploadedCount} image(s) uploaded successfully.";

                $sortedByExif = count(array_filter($filesWithMetadata, fn($f) => $f['datetime'] !== null));
                if ($sortedByExif > 0) {
                    $message .= " and sorted chronologically by capture date ({$sortedByExif} with valid dates)";
                }
                $message .= ".";

                if (count($errors) > 0) {
                    $warningMessage = $message . ' However, ' . count($errors) . ' file(s) failed.';
                    session()->flash('warning', $warningMessage);

                    if ($request->ajax()) {
                        return response()->json([
                            'redirect' => route('path.show', $path),
                            'status'   => 'warning',
                            'message'  => $warningMessage,
                            'errors'   => $errors,
                        ]);
                    }

                    return redirect()
                        ->route('path.show', $path)
                        ->with('warning', $warningMessage)
                        ->withErrors($errors);
                }

                session()->flash('success', $message);

                if ($request->ajax()) {
                    return response()->json([
                        'redirect' => route('path.show', $path),
                        'status'   => 'success',
                        'message'  => $message,
                    ]);
                }

                return redirect()
                    ->route('path.show', $path)
                    ->with('success', $message);
            });
        } catch (ValidationException $e) {
            // Handle validation errors from inside transaction
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 422);
            }
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            // Handle limit exceeded error
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 422);
            }
            return back()->withErrors(['files' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, Path $path, PathImage $pathImage = null)
    {
        $path->load(['fromRoom', 'toRoom']);

        if ($pathImage && $pathImage->path_id !== $path->id) {
            return redirect()->route('path.show', $path)
                ->with('error', 'Image does not belong to this path.');
        }

        // Handle single image vs all images
        if ($pathImage) {
            // Single image - no pagination needed
            $pathImages = collect([$pathImage]);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('pages.admin.path_images.partials.images-grid', compact('pathImages'))->render(),
                    'pagination' => '', // No pagination for single image
                ]);
            }

            return view('pages.admin.path_images.edit', compact('path', 'pathImages'));
        }

        // Multiple images - use pagination
        $pathImages = PathImage::where('path_id', $path->id)
            ->orderBy('image_order')
            ->paginate(9);

        if ($pathImages->isEmpty()) {
            return redirect()->route('path.show', $path)
                ->with('warning', 'No images found for this path.');
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('pages.admin.path_images.partials.images-grid', compact('pathImages'))->render(),
                'pagination' => $pathImages->appends(request()->query())->links('pagination::tailwind')->render(),
            ]);
        }

        return view('pages.admin.path_images.edit', compact('path', 'pathImages'));
    }

    public function update(Request $request, $pathOrPathImage = null)
    {
        if ($pathOrPathImage instanceof PathImage) {
            return $this->updateSingle($request, $pathOrPathImage);
        } else {
            return $this->updateMultiple($request, $pathOrPathImage);
        }
    }

    /**
     * Update single path image
     */
    public function updateSingle(Request $request, PathImage $pathImage)
    {
        $data = $request->validate([
            'image_order' => 'nullable|integer|min:1',
            'image_file'  => [
                'nullable',
                'image',
                'max:10240',
                'dimensions:max_width=3000,max_height=3000'
            ],
        ]);

        if (isset($data['image_order'])) {
            $pathImage->update(['image_order' => $data['image_order']]);
        }

        if ($request->hasFile('image_file')) {
            try {
                // Delete old file
                if (Storage::disk('public')->exists($pathImage->image_file)) {
                    Storage::disk('public')->delete($pathImage->image_file);
                }

                // Process and save new image
                $webpPath = $this->processAndSaveImage(
                    $request->file('image_file'),
                    $pathImage->path_id
                );

                $pathImage->update(['image_file' => $webpPath]);
            } catch (\Exception $e) {
                Log::error('PathImage conversion failed', [
                    'error' => $e->getMessage(),
                    'path_image_id' => $pathImage->id
                ]);
                return back()->withErrors([
                    'image_file' => 'Image processing failed. Please try a smaller image.'
                ]);
            }
        }

        return redirect()
            ->route('path.show', $pathImage->path)
            ->with('success', 'Image updated successfully.');
    }

    /**
     * Update multiple path images (bulk edit)
     */
    public function updateMultiple(Request $request, $pathId = null)
    {
        $pathId = $pathId ?? $request->input('path_id');
        $path = Path::findOrFail($pathId);

        $request->validate([
            'path_id' => 'required|exists:paths,id',
            'images' => 'required|array|min:1',
            'images.*.id' => 'required|exists:path_images,id',
            'images.*.image_order' => 'nullable|integer|min:1',
            'images.*.delete' => 'nullable|boolean',
        ]);

        // Validate file uploads separately
        $filesValidation = [];
        foreach ($request->file('images', []) as $index => $imageFiles) {
            if (isset($imageFiles['image_file'])) {
                $filesValidation["images.{$index}.image_file"] = [
                    'image',
                    'max:10240',
                    'dimensions:max_width=3000,max_height=3000'
                ];
            }
        }

        if (!empty($filesValidation)) {
            $request->validate($filesValidation);
        }

        $updatedCount = 0;
        $deletedCount = 0;
        $errors = [];

        foreach ($request->input('images') as $index => $imageData) {
            $pathImage = PathImage::where('id', $imageData['id'])
                ->where('path_id', $path->id)
                ->first();

            if (!$pathImage) continue;

            // Handle deletion
            if (!empty($imageData['delete'])) {
                if (Storage::disk('public')->exists($pathImage->image_file)) {
                    Storage::disk('public')->delete($pathImage->image_file);
                }
                $pathImage->delete();
                $deletedCount++;
                continue;
            }

            $updateData = [];

            if (isset($imageData['image_order']) && $imageData['image_order'] !== null) {
                $updateData['image_order'] = $imageData['image_order'];
            }

            // Handle file replacement
            if ($request->hasFile("images.{$index}.image_file")) {
                try {
                    $file = $request->file("images.{$index}.image_file");

                    // Delete old file
                    if (Storage::disk('public')->exists($pathImage->image_file)) {
                        Storage::disk('public')->delete($pathImage->image_file);
                    }

                    // Process and save new image
                    $webpPath = $this->processAndSaveImage($file, $path->id);
                    $updateData['image_file'] = $webpPath;
                } catch (\Exception $e) {
                    $errors[] = "Failed to update image {$pathImage->id}: " . $e->getMessage();
                    Log::error('PathImage update failed', [
                        'error' => $e->getMessage(),
                        'path_image_id' => $pathImage->id
                    ]);
                    continue;
                }
            }

            if (!empty($updateData)) {
                $pathImage->update($updateData);
                $updatedCount++;
            }
        }

        // ADD THIS: Reorder if any images were deleted
        if ($deletedCount > 0) {
            $this->reorderPathImages($path->id);
        }

        $message = [];
        if ($updatedCount > 0) $message[] = "{$updatedCount} image(s) updated";
        if ($deletedCount > 0) $message[] = "{$deletedCount} image(s) deleted";

        $successMessage = implode(' and ', $message) . ' successfully.';

        if (count($errors) > 0) {
            $successMessage .= ' However, some updates failed.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'redirect' => route('path.show', $path),
                'message' => $successMessage,
                'updated_count' => $updatedCount,
                'deleted_count' => $deletedCount,
                'errors' => $errors,
            ], 200);
        }

        $redirect = redirect()->route('path.show', $path)->with('success', $successMessage);

        if (count($errors) > 0) {
            $redirect->withErrors($errors);
        }

        return $redirect;
    }

    /**
     * Delete a single path image
     * 
     * @param PathImage $pathImage Image to delete
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroySingle(PathImage $pathImage)
    {
        $path = $pathImage->path;
        $pathId = $path->id; // Store this before deleting

        // Delete file from storage
        if (Storage::disk('public')->exists($pathImage->image_file)) {
            Storage::disk('public')->delete($pathImage->image_file);
        }

        $pathImage->delete();

        $this->reorderPathImages($pathId);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Image deleted successfully.',
                'status' => 'success'
            ], 200);
        }

        return redirect()
            ->route('path.show', $path)
            ->with('success', 'Image deleted successfully.');
    }

    /**
     * Delete multiple path images (bulk delete)
     * 
     * @param Request $request Must contain path_id and array of image_ids
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'path_id' => 'required|exists:paths,id',
            'image_ids' => 'required|array|min:1',
            'image_ids.*' => 'required|exists:path_images,id',
        ]);

        $path = Path::findOrFail($request->path_id);
        $deletedCount = 0;

        foreach ($request->image_ids as $imageId) {
            $pathImage = PathImage::where('id', $imageId)
                ->where('path_id', $path->id)
                ->first();

            if ($pathImage) {
                // Delete file from storage
                if (Storage::disk('public')->exists($pathImage->image_file)) {
                    Storage::disk('public')->delete($pathImage->image_file);
                }

                $pathImage->delete();
                $deletedCount++;
            }
        }

        $this->reorderPathImages($path->id);

        $successMessage = "{$deletedCount} image(s) deleted successfully.";

        if ($request->expectsJson()) {
            return response()->json([
                'redirect' => route('path.show', $path),
                'message' => $successMessage,
                'deleted_count' => $deletedCount,
                'status' => 'success'
            ], 200);
        }

        return redirect()
            ->route('path.show', $path)
            ->with('success', $successMessage);
    }

    /**
     * Reorder path images sequentially after deletion
     */
    private function reorderPathImages($pathId)
    {
        $images = PathImage::where('path_id', $pathId)
            ->orderBy('image_order')
            ->get();

        foreach ($images as $index => $image) {
            $image->image_order = $index + 1;
            $image->save(); // Individual save
        }
    }

    /**
     * Update the display order of path images
     * 
     * Used for drag-and-drop reordering functionality
     * 
     * @param Request $request Must contain array of image_orders with id and order
     * @param Path $path The path whose images are being reordered
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request, Path $path)
    {
        $request->validate([
            'image_orders' => 'required|array',
            'image_orders.*.id' => 'required|exists:path_images,id',
            'image_orders.*.order' => 'required|integer|min:1',
        ]);

        $updatedCount = 0;

        foreach ($request->image_orders as $imageData) {
            $updated = PathImage::where('id', $imageData['id'])
                ->where('path_id', $path->id)
                ->update(['image_order' => $imageData['order']]);

            if ($updated) {
                $updatedCount++;
            }
        }

        $message = $updatedCount > 0
            ? "Image order updated successfully. ({$updatedCount} images reordered)"
            : "No changes made to image order.";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'updated_count' => $updatedCount,
                'status' => 'success'
            ], 200);
        }

        return redirect()
            ->route('path.show', $path)
            ->with('success', $message);
    }

    /**
     * Process and save an uploaded image to WebP format
     * 
     * PROCESSING FLOW:
     * ┌─────────────────────────────────────────────────────────────────┐
     * │ Example: User uploads 2800x2100px, 4.5MB image                  │
     * ├─────────────────────────────────────────────────────────────────┤
     * │ 1. Frontend Compression (compressImage)                         │
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
     * │    - Already 2000px, no resize needed                           │
     * │    - Convert to WebP @ 75% quality                              │
     * │    - Output: ~700KB WebP                                        │
     * │    - Saved to storage/app/public/path_images/{id}/xxx.webp      │
     * └─────────────────────────────────────────────────────────────────┘
     * 
     * QUALITY SETTINGS:
     * - Files > 5MB: 70% quality (aggressive compression)
     * - Files ≤ 5MB: 75% quality (balanced)
     * - Lower than staff photos (80%) because path images are larger
     * 
     * DRIVER BEHAVIOR:
     * - Imagick: Handles any dimension efficiently
     * - GD: Requires 3000px pre-check to prevent memory issues
     * 
     * @param \Illuminate\Http\UploadedFile $file Image file to process
     * @param int $pathId Path ID for storage folder
     * @return string Relative path to saved WebP file
     * @throws \Exception If GD encounters oversized image or processing fails
     */
    private function processAndSaveImage($file, $pathId)
    {
        // GD driver safety check
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
        $folder = "path_images/{$pathId}";
        $webpPath = "{$folder}/{$baseName}.webp";

        // Process image
        $image = $this->manager->read($file);

        // Resize if needed (max 2000px on longest side)
        $width = $image->width();
        $height = $image->height();
        $maxDimension = 2000;

        if ($width > $maxDimension || $height > $maxDimension) {
            if ($width > $height) {
                $image->scale(width: $maxDimension);
            } else {
                $image->scale(height: $maxDimension);
            }
        }

        // Adjust quality based on file size
        $quality = 75;
        if ($file->getSize() > 5 * 1024 * 1024) {
            $quality = 70; // More aggressive for large files
        }

        // Convert to WebP
        $encodedImage = $image->encode(new WebpEncoder($quality));
        unset($image);

        // Save to storage
        Storage::disk('public')->put($webpPath, (string) $encodedImage);
        unset($encodedImage);

        return $webpPath;
    }


    /**
     * API endpoint to get current image count for a path
     */
    public function getPathImageCount(Request $request)
    {
        $request->validate([
            'path_id' => 'required|exists:paths,id',
        ]);

        $pathId = $request->input('path_id');
        $currentCount = PathImage::where('path_id', $pathId)->count();
        $maxImages = self::MAX_IMAGES_PER_PATH;
        $remaining = max(0, $maxImages - $currentCount);

        return response()->json([
            'current_count' => $currentCount,
            'max_images' => $maxImages,
            'remaining_slots' => $remaining,
            'is_full' => $remaining === 0,
        ]);
    }

    private function extractImageDateTime($file)
    {
        try {
            $filename = $file->getClientOriginalName();
            $path = $file->getRealPath();

            // NEW: Extract numeric value from filename for chronological ordering
            // Supports formats like: 1.jpg, 001.jpg, image_5.jpg, photo-123.png, etc.
            if (preg_match('/(\d+)\.(jpg|jpeg|png|gif|webp)/i', $filename, $matches)) {
                $number = intval($matches[1]);

                // Use the number as seconds offset from a base date
                // This ensures proper chronological ordering
                $baseDate = new \DateTime('2000-01-01 00:00:00');
                $baseDate->modify("+{$number} seconds");

                return $baseDate;
            }

            // Try to extract datetime from filename pattern: IMG_YYYYMMDD_HHMMSS_XXX.jpg
            if (preg_match('/IMG_(\d{8})_(\d{6})_\d+\.jpg/', $filename, $matches)) {
                $date = $matches[1]; // YYYYMMDD
                $time = $matches[2]; // HHMMSS

                $datetimeString = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2) . ' ' .
                    substr($time, 0, 2) . ':' . substr($time, 2, 2) . ':' . substr($time, 4, 2);

                $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $datetimeString);
                if ($datetime) {
                    return $datetime;
                }
            }

            // Fallback: Try to read EXIF data (in case some images have it)
            if (function_exists('exif_read_data')) {
                $exif = @exif_read_data($path, 0, true);

                if ($exif && isset($exif['EXIF']['DateTimeOriginal'])) {
                    $datetime = \DateTime::createFromFormat('Y:m:d H:i:s', $exif['EXIF']['DateTimeOriginal']);
                    if ($datetime) {
                        return $datetime;
                    }
                }
            }

            $timestamp = filemtime($path);
            if ($timestamp) {
                return (new \DateTime())->setTimestamp($timestamp);
            }
        } catch (\Exception $e) {
            Log::error('Datetime extraction error', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }
}
