<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PathController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin'])->only(['index', 'show']);
    }
    // Show all paths with their images
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'asc');
        $search = $request->get('search');

        // Handle sorting by room names and image count
        $query = Path::with(['fromRoom', 'toRoom', 'images'])
            ->whereNull('paths.deleted_at') // Specify the table name
            ->whereHas('fromRoom')
            ->whereHas('toRoom');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('fromRoom', fn($sub) =>
                $sub->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('toRoom', fn($sub) =>
                    $sub->where('name', 'like', "%{$search}%"));
            });
        }

        // Sort by related room names or image count
        if ($sort === 'from_room') {
            $query->join('rooms as from_rooms', 'paths.from_room_id', '=', 'from_rooms.id')
                ->whereNull('from_rooms.deleted_at') // Specify the table name
                ->orderBy('from_rooms.name', $direction)
                ->select('paths.*');
        } elseif ($sort === 'to_room') {
            $query->join('rooms as to_rooms', 'paths.to_room_id', '=', 'to_rooms.id')
                ->whereNull('to_rooms.deleted_at') // Specify the table name
                ->orderBy('to_rooms.name', $direction)
                ->select('paths.*');
        } elseif ($sort === 'images_count') {
            $query->withCount('images')
                ->orderBy('images_count', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $paths = $query->paginate(10)
            ->appends(['sort' => $sort, 'direction' => $direction, 'search' => $search]);

        return view('pages.admin.paths.index', compact('paths', 'sort', 'direction', 'search'));
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
    public function selection(Request $request)
    {
        $rooms = Room::orderBy('name')->get();
        $preselectedFromRoom = $request->query('from');

        return view('pages.client.navigation.selection', compact('rooms', 'preselectedFromRoom'));
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
