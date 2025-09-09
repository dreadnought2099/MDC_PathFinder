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
            ->whereNull('deleted_at') // ensure trashed paths are excluded
            ->whereHas('fromRoom')   // only paths where fromRoom exists
            ->whereHas('toRoom')     // only paths where toRoom exists
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

    // Client-side path selection page
    public function selection()
    {
        $rooms = Room::orderBy('name')->get();
        return view('pages.client.navigation.selection', compact('rooms'));
    }

    // Client-side navigation results page
    public function navigationShow(Request $request)
    {

        $fromRoomId = $request->get('from_room');
        $toRoomId = $request->get('to_room');

        // Validate
        if (!$fromRoomId || !$toRoomId) {
            return redirect()->route('paths.select')->with('error', 'Please select both starting point and destination.');
        }

        $fromRoom = Room::find($fromRoomId);
        $toRoom = Room::find($toRoomId);

        if (!$fromRoom || !$toRoom) {
            return redirect()->route('paths.select')->with('error', 'Invalid room selection.');
        }

        if ($fromRoomId == $toRoomId) {
            return redirect()->route('paths.select')->with('error', 'Starting point and destination cannot be the same.');
        }

        // Load paths with images
        $paths = Path::with(['images' => function ($query) {
            $query->orderBy('image_order');
        }])
            ->where('from_room_id', $fromRoomId)
            ->where('to_room_id', $toRoomId)
            ->get();

        return view('pages.client.navigation.results', compact('fromRoom', 'toRoom', 'paths'));
    }

    public function select()
    {
        $rooms = Room::all(); // or however you fetch rooms
        return view('paths.select', compact('rooms'));
    }

    public function results(Request $request)
    {

        // Validate the request
        $request->validate([
            'from_room' => 'required|exists:rooms,id',
            'to_room' => 'required|exists:rooms,id|different:from_room',
        ]);

        // Store the form data in session for potential return navigation
        $sessionData = [
            'from_room' => $request->from_room,
            'to_room' => $request->to_room,
        ];

        session(['last_path_search' => $sessionData]);


        $fromRoom = Room::find($request->from_room);
        $toRoom = Room::find($request->to_room);


        // Find paths directly - simple approach
        $paths = Path::with(['images' => function ($query) {
            $query->orderBy('image_order');
        }])
            ->where('from_room_id', $request->from_room)
            ->where('to_room_id', $request->to_room)
            ->get();

        return view('pages.client.navigation.results', compact('paths', 'fromRoom', 'toRoom'));
    }

    public function returnToResults()
    {
        // Check all session data first
        $allSessionData = session()->all();

        $searchData = session('last_path_search');

        // More detailed session validation
        if (!$searchData) {
            return redirect()->route('paths.select')
                ->with('message', 'No previous search found. Please make a new search.');
        }

        if (!is_array($searchData)) {
            return redirect()->route('paths.select')
                ->with('message', 'Invalid search data format. Please make a new search.');
        }

        if (!isset($searchData['from_room']) || !isset($searchData['to_room'])) {
            return redirect()->route('paths.select')
                ->with('message', 'Incomplete search data. Please make a new search.');
        }

        $fromRoomId = $searchData['from_room'];
        $toRoomId = $searchData['to_room'];

        $fromRoom = Room::find($fromRoomId);
        $toRoom = Room::find($toRoomId);

        if (!$fromRoom || !$toRoom) {
            return redirect()->route('paths.select')
                ->with('error', 'Previous search rooms no longer exist. Please make a new search.');
        }

        // Find paths directly - same logic as results() method
        $paths = Path::with(['images' => function ($query) {
            $query->orderBy('image_order');
        }])
            ->where('from_room_id', $fromRoomId)
            ->where('to_room_id', $toRoomId)
            ->get();

        return view('pages.client.navigation.results', compact('paths', 'fromRoom', 'toRoom'));
    }
}
