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
            return redirect()->route('paths.select')->with('error', 'Invalid office selection.');
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
                ->with('error', 'Previous search offices no longer exist. Please make a new search.');
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
