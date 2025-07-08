<?php

namespace App\Http\Controllers;

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
}
