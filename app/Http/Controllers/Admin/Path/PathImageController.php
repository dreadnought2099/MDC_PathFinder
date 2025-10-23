<?php

namespace App\Http\Controllers\Admin\Path;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\PathImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Drivers\Gd\Driver;

class PathImageController extends Controller
{
    private $manager;

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

        $this->manager = new ImageManager(new Driver());
    }

    public function create(Path $path = null)
    {
        $paths = Path::with(['fromRoom', 'toRoom'])->get();

        if ($paths->isEmpty()) {
            return redirect()->route('path.index')
                ->with('warning', 'No paths available. Please create a path first.');
        }

        $defaultPath = $path ?? $paths->first();

        return view('pages.admin.path_images.create', compact('paths', 'defaultPath'));
    }

    public function store(Request $request)
    {
        if (!$request->hasFile('files') || count($request->file('files')) === 0) {
            return back()->withErrors(['files' => 'Please select at least one image file.']);
        }

        $request->validate([
            'path_id' => 'required|exists:paths,id',
            'files'   => 'required|array|min:1|max:20',
            'files.*' => 'required|image|max:5120',
        ]);

        $path = Path::findOrFail($request->path_id);
        $files = $request->file('files');
        $nextOrder = PathImage::where('path_id', $path->id)->max('image_order') ?? 0;

        $uploadedCount = 0;
        $errors = [];

        foreach ($files as $file) {
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

                Log::error("Image upload failed", [
                    'file'  => $file->getClientOriginalName(),
                    'size'  => $file->getSize(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // --- Prepare messages ---
        $message = "{$uploadedCount} image(s) uploaded successfully.";

        if (count($errors) > 0) {
            $warningMessage = $message . ' However, ' . count($errors) . ' file(s) failed.';

            // Flash the warning message
            session()->flash('warning', $warningMessage);

            // If it's AJAX, respond with JSON
            if ($request->ajax()) {
                return response()->json([
                    'redirect' => route('path.show', $path),
                    'status'   => 'warning',
                    'message'  => $warningMessage,
                    'errors'   => $errors,
                ]);
            }

            // Non-AJAX fallback
            return redirect()
                ->route('path.show', $path)
                ->with('warning', $warningMessage)
                ->withErrors($errors);
        }

        // --- Success case ---
        session()->flash('success', $message);

        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('path.show', $path),
                'status'   => 'success',
                'message'  => $message,
            ]);
        }

        // Fallback for non-AJAX
        return redirect()
            ->route('path.show', $path)
            ->with('success', $message);
    }


    public function edit(Request $request, Path $path, PathImage $pathImage = null)
    {
        $path->load(['fromRoom', 'toRoom']);

        if ($pathImage && $pathImage->path_id !== $path->id) {
            return redirect()->route('path.show', $path)
                ->with('error', 'Image does not belong to this path.');
        }

        $pathImages = $pathImage
            ? collect([$pathImage])
            : PathImage::where('path_id', $path->id)->orderBy('image_order')->get();

        if ($pathImages->isEmpty()) {
            return redirect()->route('path.show', $path)
                ->with('warning', 'No images found for this path.');
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

    public function updateSingle(Request $request, PathImage $pathImage)
    {
        $data = $request->validate([
            'image_order' => 'nullable|integer|min:1',
            'image_file'  => 'nullable|image|max:5120',
        ]);

        if (isset($data['image_order'])) {
            $pathImage->update(['image_order' => $data['image_order']]);
        }

        if ($request->hasFile('image_file')) {
            try {
                // Delete old file
                Storage::disk('public')->delete($pathImage->image_file);

                // Process and save new image
                $webpPath = $this->processAndSaveImage(
                    $request->file('image_file'),
                    $pathImage->path_id
                );

                $pathImage->update(['image_file' => $webpPath]);
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to update image: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('path.show', $pathImage->path)
            ->with('success', 'Image updated successfully.');
    }

    public function updateMultiple(Request $request, $pathId = null)
    {
        $pathId = $pathId ?? $request->input('path_id');
        $path = Path::findOrFail($pathId);

        // First validate basic structure
        $request->validate([
            'path_id' => 'required|exists:paths,id',
            'images' => 'required|array|min:1',
            'images.*.id' => 'required|exists:path_images,id',
            'images.*.image_order' => 'nullable|integer|min:1',
            'images.*.delete' => 'nullable|boolean',
        ]);

        // Build dynamic validation rules for file uploads
        $fileRules = [];
        foreach ($request->file('images', []) as $index => $imageData) {
            if (isset($imageData['image_file']) && $imageData['image_file'] !== null) {
                $fileRules["images.{$index}.image_file"] = 'image|mimes:jpeg,png,jpg,webp|max:5120';
            }
        }

        // Validate files if any exist
        if (!empty($fileRules)) {
            $request->validate($fileRules, [
                'images.*.image_file.max' => 'The image file must not exceed 5MB.',
                'images.*.image_file.image' => 'The file must be an image.',
                'images.*.image_file.mimes' => 'Only JPEG, PNG, JPG, and WEBP images are allowed.',
            ]);
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
                Storage::disk('public')->delete($pathImage->image_file);
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
                    Storage::disk('public')->delete($pathImage->image_file);

                    // Process and save new image
                    $webpPath = $this->processAndSaveImage($file, $path->id);
                    $updateData['image_file'] = $webpPath;
                } catch (\Exception $e) {
                    $errors[] = "Failed to update image {$pathImage->id}: " . $e->getMessage();
                    continue;
                }
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

    public function destroySingle(PathImage $pathImage)
    {
        $path = $pathImage->path;

        Storage::disk('public')->delete($pathImage->image_file);
        $pathImage->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Image deleted successfully.'], 200);
        }

        return redirect()->route('path.show', $path)->with('success', 'Image deleted successfully.');
    }

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

    /**
     * Process and save an uploaded image
     */
    private function processAndSaveImage($file, $pathId)
    {
        $baseName = uniqid('', true);
        $folder = "path_images/{$pathId}";
        $webpPath = "{$folder}/{$baseName}.webp";

        $image = $this->manager->read($file);

        // Resize if too large
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
            $quality = 70;
        }

        $encodedImage = $image->encode(new WebpEncoder($quality));
        unset($image);

        Storage::disk('public')->put($webpPath, (string) $encodedImage);
        unset($encodedImage);

        return $webpPath;
    }
}
