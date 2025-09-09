<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ScannerController extends Controller
{
    /**
     * Show QR scanner page or room details
     */
   public function index(?Room $room = null)
    {
        // Handle invalid token scenarios
        if (request()->is('scan-marker/*') && is_null($room)) {
            return redirect()->route('scan.index')->with('error', 'Invalid room token.');
        }

        // Get fact for room or general fact
        $fact = $this->getFact($room);

        return view('pages.client.room-details.scan', compact('room', 'fact'));
    }

    /**
     * Show room details after successful scan (called by TokenController)
     */
    public function showRoom(Room $room): View
    {
        // Additional security check
        if (!Room::isValidTokenFormat($room->token)) {
            abort(404, 'Invalid room token');
        }

        // Get fact for this specific room
        $fact = $this->getFact($room);

        return view('pages.client.room-details.scan', [
            'room' => $room,
            'fact' => $fact
        ]);
    }

    /**
     * Get fact for room or general fact with caching
     */
    private function getFact(?Room $room): string
    {
        $cacheKey = $room ? "room_fact:{$room->id}" : 'general_fact';

        return Cache::remember($cacheKey, 300, function () use ($room) {
            return $this->loadFactFromFile($room);
        });
    }

    /**
     * Load fact from JSON file
     */
    private function loadFactFromFile(?Room $room): string
    {
        $defaultFact = "No history facts available at the moment.";
        $filePath = storage_path('app/facts.json');

        if (!file_exists($filePath)) {
            Log::warning('Facts file not found', ['path' => $filePath]);
            return $defaultFact;
        }

        try {
            $facts = json_decode(file_get_contents($filePath), true, 512, JSON_THROW_ON_ERROR);

            if (!$facts) {
                return $defaultFact;
            }

            // Try to get office-specific fact first
            if ($room && $room->name) {
                $officeFacts = $facts['offices'][$room->name] ?? null;
                if (!empty($officeFacts) && is_array($officeFacts)) {
                    return $officeFacts[array_rand($officeFacts)];
                }
            }

            // Fallback to general facts
            $generalFacts = $facts['general'] ?? null;
            if (!empty($generalFacts) && is_array($generalFacts)) {
                return $generalFacts[array_rand($generalFacts)];
            }

            return $defaultFact;
        } catch (\JsonException $e) {
            Log::error('Invalid facts JSON file', [
                'error' => $e->getMessage(),
                'file' => $filePath
            ]);
            return $defaultFact;
        } catch (\Exception $e) {
            Log::error('Error loading facts', [
                'error' => $e->getMessage(),
                'room_id' => $room?->id
            ]);
            return $defaultFact;
        }
    }

    /**
     * Clear facts cache (useful for admin operations)
     */
    public function clearFactsCache(): void
    {
        Cache::forget('general_fact');

        // Clear room-specific fact cache
        Room::all()->each(function ($room) {
            Cache::forget("room_fact:{$room->id}");
        });
    }
}
