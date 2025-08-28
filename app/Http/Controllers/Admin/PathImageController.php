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
    public function create()
    {
        // Get all automated paths for the dropdown
        $paths = Path::with(['fromRoom', 'toRoom'])->get();

        // Optionally, pick the first path as a default if you want a "Path Info" card
        $defaultPath = $paths->first(); // can be null if no paths exist

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
        $files = $request->file('files');

        $nextOrder = PathImage::where('path_id', $path->id)->max('image_order') ?? 0;

        foreach ($files as $file) {
            $imagePath = $file->store('path_images', 'public');

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

    // Show form to edit/update an image
    public function edit(PathImage $pathImage)
    {
        return view('pages.admin.path_images.edit', compact('pathImage'));
    }

    // Update image order or replace image file
    public function update(Request $request, PathImage $pathImage)
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
