<?php

namespace App\Http\Controllers\Admin\Path;

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
        // Use validated inputs or fallback to session
        $sort = $request->input('sort', session('paths.sort', 'id'));
        $direction = strtolower($request->input('direction', session('paths.direction', 'asc'))) === 'desc' ? 'desc' : 'asc';
        $search = trim($request->input('search', session('paths.search', '')));

        // Save preferences to session
        session([
            'paths.sort' => $sort,
            'paths.direction' => $direction,
            'paths.search' => $search,
        ]);

        // Build query using model scopes
        $query = Path::query()
            ->with(['fromRoom', 'toRoom'])
            ->whereNull('paths.deleted_at')
            ->whereHas('fromRoom')
            ->whereHas('toRoom')
            ->search($search)
            ->sortBy($sort, $direction);

        // Paginate and keep query string
        $paths = $query->paginate(10)->withQueryString();

        // AJAX response
        if ($request->ajax()) {
            return response()->json([
                'html' => view('pages.admin.paths.partials.path-table', compact('paths'))->render(),
            ]);
        }

        // Full view
        return view('pages.admin.paths.index', compact('paths', 'sort', 'direction', 'search'));
    }

    // Show images for a specific path
    public function show(Path $path, Request $request)
    {
        $path->load(['fromRoom', 'toRoom']);

        // Get search parameters
        $search = $request->input('search', '');
        $sort = $request->input('sort', 'image_order');
        $direction = $request->input('direction', 'asc');

        $imagesQuery = $path->images();

        if ($request->filled('search')) {
            // Only search by numeric image_order
            if (is_numeric($search)) {
                $imagesQuery->where('image_order', $search);
            } else {
                // If search is not numeric, return an empty result
                $imagesQuery->whereRaw('0 = 1'); // always false
            }
        }

        $images = $imagesQuery
            ->orderBy($sort, $direction)
            ->paginate(8)
            ->withQueryString();

        // AJAX request (Alpine)
        if ($request->ajax()) {
            $html = view('pages.admin.paths.partials.path-images-table', compact('path', 'images'))->render();
            return response()->json(['html' => $html]);
        }

        return view('pages.admin.paths.show', compact('path', 'images', 'search', 'sort', 'direction'));
    }

    // Client-side path selection page
    public function selection(Request $request)
    {
        // CHANGED: Only get entrance points for "from" dropdown
        $entrancePoints = Room::where('room_type', 'entrance_point')
            ->orderBy('name')
            ->get();

        // CHANGED: Get all regular rooms for "to" dropdown
        $regularRooms = Room::where('room_type', 'regular')
            ->orderBy('name')
            ->get();

        // CHANGED: Validate preselected entrance point
        $preselectedFromRoom = $request->query('from');
        if ($preselectedFromRoom) {
            $preselectedRoom = Room::find($preselectedFromRoom);
            if (!$preselectedRoom || $preselectedRoom->room_type !== 'entrance_point') {
                $preselectedFromRoom = null; // Invalid entrance point
            }
        }

        return view('pages.client.navigation.selection', compact('entrancePoints', 'regularRooms', 'preselectedFromRoom'));
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
            return redirect()->route('paths.select')->with('error', 'Invalid office selection.');
        }

        // ADDED: Validate that starting point is an entrance
        if ($fromRoom->room_type !== 'entrance_point') {
            return redirect()->route('paths.select')->with('error', 'Starting point must be an entrance point.');
        }

        // ADDED: Validate that destination is a regular room
        if ($toRoom->room_type !== 'regular') {
            return redirect()->route('paths.select')->with('error', 'Destination must be a regular office/room.');
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
        // CHANGED: Enhanced validation
        $request->validate([
            'from_room' => 'required|exists:rooms,id',
            'to_room' => 'required|exists:rooms,id|different:from_room',
        ]);

        $fromRoom = Room::find($request->from_room);
        $toRoom = Room::find($request->to_room);

        // ADDED: Validate room types
        if ($fromRoom->room_type !== 'entrance_point') {
            return back()->withErrors(['from_room' => 'Starting point must be an entrance point.'])->withInput();
        }

        if ($toRoom->room_type !== 'regular') {
            return back()->withErrors(['to_room' => 'Destination must be a regular office/room.'])->withInput();
        }

        // Store the form data in session for potential return navigation
        $sessionData = [
            'from_room' => $request->from_room,
            'to_room' => $request->to_room,
        ];

        session(['last_path_search' => $sessionData]);

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
                ->with('error', 'Previous search offices no longer exist. Please make a new search.');
        }

        // ADDED: Validate room types for stored session
        if ($fromRoom->room_type !== 'entrance_point' || $toRoom->room_type !== 'regular') {
            return redirect()->route('paths.select')
                ->with('error', 'Previous search is invalid. Please make a new search.');
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