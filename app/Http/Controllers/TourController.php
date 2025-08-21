<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $room = null;

        if ($request->has('room')) {
            $room = Room::find($request->room);
        }

        // Load JSON
        $fact = null;
        $filePath = storage_path('app/facts.json');

        if (file_exists($filePath)) {
            $facts = json_decode(file_get_contents($filePath), true);

            // Use office name instead of room id
            $officeName = $room->name; // assuming $room->name matches the JSON key

            if (isset($facts['offices'][$officeName]) && count($facts['offices'][$officeName]) > 0) {
                // Random fact for this office
                $officeFacts = $facts['offices'][$officeName];
                $fact = $officeFacts[array_rand($officeFacts)];
            } elseif (isset($facts['general']) && count($facts['general']) > 0) {
                // Random general campus fact
                $generalFacts = $facts['general'];
                $fact = $generalFacts[array_rand($generalFacts)];
            } else {
                $fact = "No history facts available at the moment.";
            }
        } else {
            $fact = "No history facts available at the moment.";
        }


        return view('pages.client.scan', compact('room', 'fact'));
    }
}
