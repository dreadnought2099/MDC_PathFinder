<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\PathImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Drivers\Gd\Driver;

class PathImageController extends Controller
{
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
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');
    }

    // Show form to upload images
    public function create(Path $path = null)
    {
        // Get all paths for the dropdown
        $paths = Path::with(['fromRoom', 'toRoom'])->get();

        if ($paths->isEmpty()) {
            return redirect()->route('path.index')
                ->with('warning', 'No paths available. Please create a path first.');
        }

        // Fixed: Remove the non-existent ->exists property
        $defaultPath = $path ?? $paths->first();

        return view('pages.admin.path_images.create', compact('paths', 'defaultPath'));
    }

    // Store multiple images for a path
    public function store(Request $request)
    {
        if (!$request->hasFile('files') || count($request->file('files')) === 0) {
            return back()->withErrors(['files' => 'Please select at least one image file.']);
        }

        $request->validate([
            'path_id' => 'required|exists:paths,id',
            'files'   => 'required|array|min:1|max:20',
            'files.*' => 'required|image|max:10240', // Reduced to 10MB for safety
        ]);

        $path = Path::findOrFail($request->path_id);
        $files = $request->file('files');
        $nextOrder = PathImage::where('path_id', $path->id)->max('image_order') ?? 0;

        $manager = new ImageManager(new Driver());

        // Track successful uploads
        $uploadedCount = 0;
        $errors = [];

        foreach ($files as $index => $file) {
            try {
                // Temporarily increase memory for this operation
                $currentMemory = ini_get('memory_limit');
                ini_set('memory_limit', '512M');

                $baseName = uniqid('', true);
                $folder = "path_images/{$path->id}";
                $webpPath = "{$folder}/{$baseName}.webp";

                // Read and resize image to prevent memory issues
                $image = $manager->read($file);

                // Get original dimensions
                $width = $image->width();
                $height = $image->height();

                // Resize if image is too large (max 2000px on longest side)
                $maxDimension = 2000;
                if ($width > $maxDimension || $height > $maxDimension) {
                    if ($width > $height) {
                        $image->scale(width: $maxDimension);
                    } else {
                        $image->scale(height: $maxDimension);
                    }
                }

                // Encode to WebP with quality adjustment based on size
                $quality = 70;
                if ($file->getSize() > 5 * 1024 * 1024) { // If > 5MB
                    $quality = 60; // Lower quality for larger files
                }

                $encodedImage = $image->encode(new WebpEncoder($quality));

                // Free memory
                unset($image);

                Storage::disk('public')->put($webpPath, (string) $encodedImage);

                // Free memory
                unset($encodedImage);

                PathImage::create([
                    'path_id'     => $path->id,
                    'image_file'  => $webpPath,
                    'image_order' => ++$nextOrder,
                ]);

                $uploadedCount++;

                // Restore original memory limit
                ini_set('memory_limit', $currentMemory);

                // Force garbage collection after each image
                gc_collect_cycles();
            } catch (\Exception $e) {
                // Restore memory limit on error
                if (isset($currentMemory)) {
                    ini_set('memory_limit', $currentMemory);
                }

                $errors[] = "Failed to upload {$file->getClientOriginalName()}: " . $e->getMessage();

                // Log the error
                \Log::error("Image upload failed", [
                    'file' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'error' => $e->getMessage()
                ]);

                // Continue with next file instead of failing completely
                continue;
            }
        }

        // Build success message
        $message = "{$uploadedCount} image(s) uploaded successfully.";

        if (count($errors) > 0) {
            return redirect()
                ->route('path.show', $path)
                ->with('warning', $message . ' However, ' . count($errors) . ' file(s) failed.')
                ->withErrors($errors);
        }

        return redirect()
            ->route('path.show', $path)
            ->with('success', $message);
    }


    // Show form to edit single or multiple images
    public function edit(Request $request, Path $path, PathImage $pathImage = null)
    {
        $path->load(['fromRoom', 'toRoom']);
        $pathImages = $pathImage && $pathImage->exists
            ? collect([$pathImage->path_id === $path->id ? $pathImage : null])->filter()
            : PathImage::where('path_id', $path->id)->orderBy('image_order')->get();

        if ($pathImages->isEmpty()) {
            return redirect()->route('path.show', $path)
                ->with('warning', 'No images found for this path.');
        }

        return view('pages.admin.path_images.edit', compact('path', 'pathImages'));
    }

    // Update multiple images (orders and/or files)
    public function update(Request $request, $pathOrPathImage = null)
    {
        // Handle both single and multiple image updates
        if ($pathOrPathImage instanceof PathImage) {
            return $this->updateSingle($request, $pathOrPathImage);
        } else {
            return $this->updateMultiple($request, $pathOrPathImage);
        }
    }

    // Update single image (backward compatibility)
    public function updateSingle(Request $request, PathImage $pathImage)
    {
        $data = $request->validate([
            'image_order' => 'nullable|integer|min:1',
            'image_file'  => 'nullable|image|max:51200',
        ]);

        if (isset($data['image_order'])) {
            $pathImage->update(['image_order' => $data['image_order']]);
        }

        if ($request->hasFile('image_file')) {
            // Delete old files
            Storage::disk('public')->delete($pathImage->image_file);

            // Process new upload (save original + webp)
            $manager = new ImageManager(new Driver());
            $file = $request->file('image_file');
            $file->getClientOriginalExtension();
            $baseName = uniqid('', true);

            $folder   = "path_images/{$pathImage->path_id}";
            $webpPath = "{$folder}/{$baseName}.webp";

            // Save webp
            $image = $manager->read($file)->encode(new WebpEncoder(70));
            Storage::disk('public')->put($webpPath, (string) $image);

            $pathImage->update([
                'image_file'    => $webpPath,
            ]);
        }

        $path = $pathImage->path;
        return redirect()->route('path.show', $path)->with('success', 'Image updated successfully.');
    }

    // Update multiple images
    public function updateMultiple(Request $request, $pathId = null)
    {
        $pathId = $pathId ?? $request->input('path_id');
        $path = Path::findOrFail($pathId);

        $request->validate([
            'path_id' => 'required|exists:paths,id',
            'images' => 'required|array|min:1',
            'images.*.id' => 'required|exists:path_images,id',
            'images.*.image_order' => 'nullable|integer|min:1',
            'images.*.image_file' => 'nullable|image|max:51200',
            'images.*.delete' => 'nullable|boolean',
        ]);

        $updatedCount = 0;
        $deletedCount = 0;

        foreach ($request->input('images') as $imageData) {
            $pathImage = PathImage::where('id', $imageData['id'])
                ->where('path_id', $path->id)
                ->first();

            if (!$pathImage) continue;

            // Handle deletion
            if (!empty($imageData['delete'])) {
                Storage::disk('public')->delete($pathImage->image_file);

                $pathImage->delete();
                $deletedCount++;
                continue;
            }

            $updateData = [];

            if (isset($imageData['image_order']) && $imageData['image_order'] !== null) {
                $updateData['image_order'] = $imageData['image_order'];
            }

            // Replace image file
            if ($request->hasFile("images.{$imageData['id']}.image_file")) {
                $file = $request->file("images.{$imageData['id']}.image_file");

                // Delete old files
                Storage::disk('public')->delete($pathImage->image_file);

                // Process new upload
                $manager = new ImageManager(new Driver());
                $file->getClientOriginalExtension();
                $baseName = uniqid('', true);

                $folder   = "path_images/{$path->id}";
                $webpPath = "{$folder}/{$baseName}.webp";

                // Save webp
                $image = $manager->read($file)->encode(new WebpEncoder(70));
                Storage::disk('public')->put($webpPath, (string) $image);

                $updateData['image_file']    = $webpPath;
            }

            if (!empty($updateData)) {
                $pathImage->update($updateData);
                $updatedCount++;
            }
        }

        $message = [];
        if ($updatedCount > 0) $message[] = "{$updatedCount} image(s) updated";
        if ($deletedCount > 0) $message[] = "{$deletedCount} image(s) deleted";

        $successMessage = implode(' and ', $message) . ' successfully.';

        if ($request->expectsJson()) {
            return response()->json([
                'redirect' => route('path.show', $path),
                'message' => $successMessage,
                'updated_count' => $updatedCount,
                'deleted_count' => $deletedCount,
            ], 200);
        }

        return redirect()->route('path.show', $path)->with('success', $successMessage);
    }


    // Delete a specific image
    public function destroySingle(PathImage $pathImage)
    {
        $path = $pathImage->path;

        // Delete both webp
        Storage::disk('public')->delete($pathImage->image_file);

        $pathImage->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Image deleted successfully.'], 200);
        }

        return redirect()->route('path.show', $path)->with('success', 'Image deleted successfully.');
    }

    // Delete multiple images
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
                // Delete both webp
                Storage::disk('public')->delete($pathImage->image_file);

                $pathImage->delete();
                $deletedCount++;
            }
        }

        $successMessage = "{$deletedCount} image(s) deleted successfully.";

        if ($request->expectsJson()) {
            return response()->json([
                'redirect' => route('path.show', $path),
                'message' => $successMessage,
                'deleted_count' => $deletedCount,
            ], 200);
        }

        return redirect()->route('path.show', $path)->with('success', $successMessage);
    }


    // Bulk update image orders
    public function updateOrder(Request $request, Path $path)
    {
        $request->validate([
            'image_orders' => 'required|array',
            'image_orders.*.id' => 'required|exists:path_images,id',
            'image_orders.*.order' => 'required|integer|min:1',
        ]);

        foreach ($request->image_orders as $imageData) {
            PathImage::where('id', $imageData['id'])
                ->where('path_id', $path->id)
                ->update(['image_order' => $imageData['order']]);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Image order updated successfully.'], 200);
        }

        return redirect()->route('path.show', $path)->with('success', 'Image order updated successfully.');
    }
}
