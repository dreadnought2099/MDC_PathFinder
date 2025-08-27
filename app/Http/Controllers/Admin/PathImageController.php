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
        $data = $request->validate([
            'path_id' => 'required|exists:paths,id',
            'file'    => 'required|image|max:2048',
        ]);

        $path = $request->file('file')->store('path_images', 'public');

        PathImage::create([
            'path_id'   => $data['path_id'],
            'file_path' => $path,
        ]);

        return redirect()->route('path_images.index')->with('success', 'Image uploaded successfully.');
    }

    public function destroy(PathImage $pathImage)
    {
        Storage::disk('public')->delete($pathImage->file_path);
        $pathImage->delete();

        return redirect()->route('path_images.index')->with('success', 'Image deleted.');
    }
}
