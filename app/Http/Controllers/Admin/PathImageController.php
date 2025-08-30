<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\PathImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PathImageController extends Controller
{
    // Show form to upload images
    public function create(Path $path = null)
    {
        // Get all paths for the dropdown
        $paths = Path::with(['fromRoom', 'toRoom'])->get();

        if ($paths->isEmpty()) {
            return redirect()->route('path.index')
                ->with('warning', 'No paths available. Please create a path first.');
        }

        // Default: use route param if provided, otherwise first path
        $defaultPath = $path && $path->exists ? $path : $paths->first();

        return view('pages.admin.path_images.create', compact('paths', 'defaultPath'));
    }

    // Store multiple images for a path
    public function store(Request $request)
    {
        $request->validate([
            'path_id' => 'required|exists:paths,id',
            'files'   => 'required|array|min:1',
            'files.*' => 'required|image|max:51200',
        ]);

        $path = Path::findOrFail($request->path_id);

        // Rest of your existing logic stays the same
        $files = $request->file('files');
        $nextOrder = PathImage::where('path_id', $path->id)->max('image_order') ?? 0;

        foreach ($files as $file) {
            $imagePath = $file->store('path_images/' . $path->id, 'public');

            PathImage::create([
                'path_id' => $path->id,
                'image_file' => $imagePath,
                'image_order' => ++$nextOrder,
            ]);
        }

        $successMessage = "Images for Path {$path->fromRoom->name} → {$path->toRoom->name} uploaded successfully.";

        if ($request->expectsJson()) {
            return response()->json([
                'redirect' => route('path.show', $path),
                'message' => $successMessage,
            ], 200);
        }

        return redirect()->route('path.show', $path)->with('success', $successMessage);
    }

    // Show form to edit multiple images for a path
    public function edit(Request $request, $pathOrPathImage = null)
    {
        // Handle both single image edit and multiple images edit
        if ($pathOrPathImage instanceof PathImage) {
            // Single image edit (backward compatibility)
            $pathImage = $pathOrPathImage;
            $path = $pathImage->path;
            $pathImages = collect([$pathImage]);
        } else {
            // Multiple images edit by path
            $pathId = $pathOrPathImage ?? $request->input('path_id');

            if (!$pathId) {
                // If no path specified, show path selection
                $paths = Path::with(['fromRoom', 'toRoom'])->get();
                return view('pages.admin.path_images.select_path', compact('paths'));
            }

            $path = Path::findOrFail($pathId);
            $pathImages = PathImage::where('path_id', $path->id)
                ->orderBy('image_order')
                ->get();

            // Kung walay path images assigned to that specific path
            // It will redirect to showing the path navigation — path.show
            if ($pathImages->isEmpty()) {
                return redirect()->route('path.show', $path)
                    ->with('warning', 'No images found for this path. Redirected to Path Details.');
            }
        }

        return view('pages.admin.path_images.edit', compact('pathImages', 'path'));
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
    private function updateSingle(Request $request, PathImage $pathImage)
    {
        $data = $request->validate([
            'image_order' => 'nullable|integer|min:1',
            'image_file'  => 'nullable|image|max:51200',
        ]);

        // Update image order if provided
        if (isset($data['image_order'])) {
            $pathImage->update(['image_order' => $data['image_order']]);
        }

        // Replace image file if new one is uploaded
        if ($request->hasFile('image_file')) {
            // Delete old image
            Storage::disk('public')->delete($pathImage->image_file);

            // Store new image
            $newImagePath = $request->file('image_file')->store('path_images', 'public');
            $pathImage->update(['image_file' => $newImagePath]);
        }

        $path = $pathImage->path;
        return redirect()->route('path.show', $path)->with('success', 'Image updated successfully.');
    }

    // Update multiple images
    private function updateMultiple(Request $request, $pathId = null)
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

            if (!$pathImage) {
                continue; // Skip if image doesn't exist or doesn't belong to this path
            }

            // Check if image should be deleted
            if (!empty($imageData['delete'])) {
                Storage::disk('public')->delete($pathImage->image_file);
                $pathImage->delete();
                $deletedCount++;
                continue;
            }

            $updateData = [];

            // Update image order if provided
            if (isset($imageData['image_order']) && $imageData['image_order'] !== null) {
                $updateData['image_order'] = $imageData['image_order'];
            }

            // Replace image file if new one is uploaded
            if ($request->hasFile("images.{$imageData['id']}.image_file")) {
                // Delete old image
                Storage::disk('public')->delete($pathImage->image_file);

                // Store new image
                $newImagePath = $request->file("images.{$imageData['id']}.image_file")
                    ->store('path_images', 'public');
                $updateData['image_file'] = $newImagePath;
            }

            if (!empty($updateData)) {
                $pathImage->update($updateData);
                $updatedCount++;
            }
        }

        $message = [];
        if ($updatedCount > 0) {
            $message[] = "{$updatedCount} image(s) updated";
        }
        if ($deletedCount > 0) {
            $message[] = "{$deletedCount} image(s) deleted";
        }

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
    public function destroy(PathImage $pathImage)
    {
        $path = $pathImage->path;

        // Delete the file from storage
        Storage::disk('public')->delete($pathImage->image_file);

        // Delete the database record
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
                // Delete the file from storage
                Storage::disk('public')->delete($pathImage->image_file);
                // Delete the database record
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

    // Bulk update image orders (for drag-and-drop reordering)
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
