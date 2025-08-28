<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\Room;
use Illuminate\Http\Request;

class PathController extends Controller
{
    // Show all paths with their images
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'asc');

        $paths = Path::with(['fromRoom', 'toRoom'])
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->appends(['sort' => $sort, 'direction' => $direction]);

        return view('pages.admin.paths.index', compact('paths', 'sort', 'direction'));
    }

    // Show images for a specific path
    public function show(Path $path)
    {
        $path->load(['fromRoom', 'toRoom', 'images' => function ($query) {
            $query->orderBy('image_order');
        }]);

        return view('pages.admin.paths.show', compact('path'));
    }

    public function destroy(Path $path)
    {
        // Optional: manually delete associated images if not using cascade delete
        // foreach ($path->images as $image) {
        //     Storage::disk('public')->delete($image->image_file);
        // }
        // $path->images()->delete();

        $path->delete();
        return redirect()->route('path.index')->with('success', 'Path deleted successfully.');
    }
}
