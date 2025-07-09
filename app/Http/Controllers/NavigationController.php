<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use App\Models\Path;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function index()
    {
        return view('ar.scan');
    }

    public function showAvailableRooms($markerIdentifier)
    {
        // Find marker record by unique marker ID string
        $marker = \App\Models\Marker::where('marker_id', $markerIdentifier)->firstOrFail();

        $rooms = \App\Models\Room::with('marker')->get();

        return view('ar.select-room', [
            'rooms' => $rooms,
            'markerId' => $marker->id, // Use numeric ID for comparison
            'markerIdentifier' => $marker->marker_id // optional, for UI
        ]);
    }

    public function navigateToRoom($sourceMarkerId, $roomId)
    {
        $sourceMarker = \App\Models\Marker::where('marker_id', $sourceMarkerId)->firstOrFail();
        $room = \App\Models\Room::with('marker')->findOrFail($roomId);
        $targetMarker = $room->marker;

        $paths = \App\Models\Path::with(['fromMarker', 'toMarker'])->get();
        $markers = \App\Models\Marker::all();

        return view('ar.navigate', compact('paths', 'markers', 'sourceMarker', 'targetMarker', 'room'));
    }
}
