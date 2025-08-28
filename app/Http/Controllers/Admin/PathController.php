<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\Room;
use Illuminate\Http\Request;

class PathController extends Controller
{
    // Show all paths
    public function index()
    {

        $paths = Path::with(['fromRoom', 'toRoom'])->paginate(10);

        return view('pages.admin.paths.index', compact('paths'));
    }

    // Show forms to create new path
    public function create()
    {

        $rooms = Room::all();

        return view('pages.admin.paths.create', compact('rooms'));
    }

    // Store new path   
    public function store(Request $request)
    {
        $data = $request->validate([
            'from_room_id' => 'required|exists:rooms,id',
            'to_room_id'   => 'required|exists:rooms,id',
        ]);

        Path::create($data);

        return redirect()->route('path.index')->with('success', 'Path created successfully.');
    }

    public function destroy(Path $path)
    {

        $path->delete();
        return redirect()->route('path.index')->with('success', 'Path deleted successfully.');
    }
}
