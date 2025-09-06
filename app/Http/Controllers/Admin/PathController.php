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

    /*
        API: Get all rooms for selection dropdowns
        (API for dropdowns)
        Scenario: If later you want to populate the Starting Point 
        and Destination dropdowns dynamically via AJAX instead of passing $rooms from the controller.
    */
    public function getRooms()
    {
        $rooms = Room::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($rooms);
    }

    /* API: Get popular/available paths
        API for suggested/common routes)
        Scenario: If you want to show “Suggested routes” or “Most used paths” on the homepage or dashboard.
        Example: mobile app integration showing “popular routes” list.
    */
    public function getPopularPaths()
    {
        $paths = Path::with(['fromRoom', 'toRoom'])
            ->whereHas('images') // Only paths with images
            ->get()
            ->map(function ($path) {
                return [
                    'id' => $path->id,
                    'from_room_id' => $path->from_room_id,
                    'to_room_id' => $path->to_room_id,
                    'from_room' => $path->fromRoom->name ?? 'Unknown',
                    'to_room' => $path->toRoom->name ?? 'Unknown',
                    'total_images' => $path->images()->count()
                ];
            });

        return response()->json($paths);
    }

    /* API: Get navigation data for specific route
        (API version of navigationShow)
        Scenario: If you want your frontend (Vue/React/mobile app) to fetch paths via 
        JSON and render them dynamically, instead of loading a Blade view.
        Example: An AR/VR navigation app that needs JSON path + images.
    */
    public function getNavigationRoute(Request $request)
    {
        $fromRoomId = $request->get('from');
        $toRoomId = $request->get('to');

        if (!$fromRoomId || !$toRoomId) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        // Find path between the two rooms
        $path = Path::with(['fromRoom', 'toRoom', 'images' => function ($query) {
            $query->orderBy('image_order');
        }])
            ->where('from_room_id', $fromRoomId)
            ->where('to_room_id', $toRoomId)
            ->first();

        if (!$path) {
            return response()->json(['error' => 'Path not found'], 404);
        }

        return response()->json([
            'path_id' => $path->id,
            'from_room' => $path->fromRoom->name ?? 'Unknown',
            'to_room' => $path->toRoom->name ?? 'Unknown',
            'images' => $path->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('storage/' . $image->image_path),
                    'order' => $image->image_order,
                    'description' => $image->description ?? 'Continue along this path'
                ];
            })
        ]);
    }

    /* API: Get path navigation data by path ID (for backward compatibility)
        (API by path ID)
        Scenario: Useful for mobile apps or a single-page app that just needs path details 
        for one specific ID, instead of by from/to rooms.
        Example: “Resume navigation on path #5” feature.
    */
    public function getPathNavigation(Path $path)
    {
        $path->load(['fromRoom', 'toRoom', 'images' => function ($query) {
            $query->orderBy('image_order');
        }]);

        return response()->json([
            'path_id' => $path->id,
            'from_room' => $path->fromRoom->name ?? 'Unknown',
            'to_room' => $path->toRoom->name ?? 'Unknown',
            'images' => $path->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('storage/' . $image->image_path),
                    'order' => $image->image_order,
                    'description' => $image->description ?? 'Continue along this path'
                ];
            })
        ]);
    }

    /* API: Get all available navigation paths (for backward compatibility)
        (API: list all paths)
        Scenario: If you want a route explorer or admin dashboard that shows all available navigation 
        paths with thumbnails, in a separate app or frontend component.
        Example: React dashboard fetching /api/paths.
    */
    public function getNavigationPaths()
    {
        $paths = Path::with(['fromRoom', 'toRoom', 'images' => function ($query) {
            $query->orderBy('image_order')->limit(1); // Get first image as thumbnail
        }])->get();

        return response()->json([
            'paths' => $paths->map(function ($path) {
                return [
                    'id' => $path->id,
                    'from_room' => $path->fromRoom->name ?? 'Unknown',
                    'to_room' => $path->toRoom->name ?? 'Unknown',
                    'thumbnail' => $path->images->first() ? asset('storage/' . $path->images->first()->image_path) : null,
                    'total_images' => $path->images()->count()
                ];
            })
        ]);
    }
}
