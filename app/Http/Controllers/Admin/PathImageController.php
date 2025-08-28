<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\PathImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PathImageController extends Controller
{
    public function index()
    {
        $images = PathImage::with('path')->get();
        return view('pages.admin.path_images.index', compact('images'));
    }

    public function create()
    {
        $paths = Path::with(['fromRoom', 'toRoom'])->get();
        return view('pages.admin.path_images.create', compact('paths'));
    }

    public function store(Request $request)
    {
        // Validate multiple files
        $data = $request->validate([
            'path_id' => 'required|exists:paths,id',
            'file.*'    => 'required|image|max:51200',
        ]);

        $pathModel = Path::findOrFail($data['path_id']);
        $files = $request->file('files');

        if (!$files) {
            return redirect()->back()->withErrors(['files' => 'Please select at least one image.']);
        }

        // Get the next image order for this path
        $nextOrder = PathImage::where('path_id', $data['path_id'])->max('image_order') ?? 0;

        foreach ($files as $file) {
            $path = $file->store('path_images', 'public');

            PathImage::create([
                'path_id'     => $data['path_id'],
                'image_file'  => $path,
                'image_order' => ++$nextOrder, // increment order for each image
            ]);
        }

        session()->flash('success', "Images for Path {$pathModel->fromRoom->name} → {$pathModel->toRoom->name} uploaded successfully.");

        if ($request->expectsJson()) {
            return response()->json([
                'redirect' => route('path_images.index'),
                'flash' => session('success'),
            ], 200);
        }

        return redirect()->route('path_images.index')->with('success', 'Image uploaded successfully.');
    }

    public function destroy(PathImage $pathImage)
    {
        Storage::disk('public')->delete($pathImage->image_file);
        $pathImage->delete();

        return redirect()->route('path_images.index')->with('success', 'Image deleted successfully.');
    }
}
