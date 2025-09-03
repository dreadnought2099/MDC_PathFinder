<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScannerController extends Controller
{
    public function index(?Room $room = null)
    {
        if (request()->is('scan-marker/*') && is_null($room)) {
            return redirect()->route('scan.index')->with('error', 'Invalid room token.');
        }

        // Load JSON
        $fact = "No history facts available at the moment.";
        $filePath = storage_path('app/facts.json');

        if (file_exists($filePath)) {
            $facts = json_decode(file_get_contents($filePath), true);
            $officeName = $room?->name;

            if ($officeName && !empty($facts['offices'][$officeName])) {
                $officeFacts = $facts['offices'][$officeName];
                $fact = $officeFacts[array_rand($officeFacts)];
            } elseif (!empty($facts['general'])) {
                $generalFacts = $facts['general'];
                $fact = $generalFacts[array_rand($generalFacts)];
            }
        }

        return view('pages.client.scan', compact('room', 'fact'));
    }
}
