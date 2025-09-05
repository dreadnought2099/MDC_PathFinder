<?php

namespace App\Services;

use App\Models\Path;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntranceGateService
{
    /**
     * Create an entrance gate and auto-connect it to all existing rooms
     */
    public function createEntranceGate(array $roomData)
    {
        return DB::transaction(function () use ($roomData) {
            // Create the entrance gate room
            $entranceGate = Room::create(array_merge($roomData, [
                'room_type' => 'entrance_gate'
            ]));

            // Get all regular rooms (excluding other entrance gates)
            $regularRooms = Room::where('room_type', 'regular')
                ->where('id', '!=', $entranceGate->id)
                ->get();

            $pathsCreated = 0;

            // Create bidirectional paths between entrance gate and all regular rooms
            foreach ($regularRooms as $room) {
                // Path from entrance gate to room
                $path1 = Path::firstOrCreate([
                    'from_room_id' => $entranceGate->id,
                    'to_room_id' => $room->id,
                ]);

                // Path from room to entrance gate (bidirectional)
                $path2 = Path::firstOrCreate([
                    'from_room_id' => $room->id,
                    'to_room_id' => $entranceGate->id,
                ]);

                if ($path1->wasRecentlyCreated) $pathsCreated++;
                if ($path2->wasRecentlyCreated) $pathsCreated++;
            }

            Log::info("Created entrance gate '{$entranceGate->name}' with {$pathsCreated} new paths");

            return [
                'room' => $entranceGate,
                'paths_created' => $pathsCreated,
                'rooms_connected' => $regularRooms->count()
            ];
        });
    }

    /**
     * Auto-connect entrance gates to a new regular room
     */
    public function connectNewRoomToAllRooms(Room $newRoom)
    {
        $otherRooms = Room::where('id', '!=', $newRoom->id)->get();
        $pathsCreated = 0;

        foreach ($otherRooms as $room) {
            $path1 = Path::firstOrCreate([
                'from_room_id' => $newRoom->id,
                'to_room_id'   => $room->id,
            ]);

            $path2 = Path::firstOrCreate([
                'from_room_id' => $room->id,
                'to_room_id'   => $newRoom->id,
            ]);

            if ($path1->wasRecentlyCreated) $pathsCreated++;
            if ($path2->wasRecentlyCreated) $pathsCreated++;
        }

        return [
            'paths_created'   => $pathsCreated,
            'rooms_connected' => $otherRooms->count(),
        ];
    }

    /**
     * Remove all paths connected to an entrance gate
     */
    public function removeEntranceGatePaths(Room $entranceGate)
    {
        if ($entranceGate->room_type !== 'entrance_gate') {
            return;
        }

        Path::where('from_room_id', $entranceGate->id)
            ->orWhere('to_room_id', $entranceGate->id)
            ->delete();
    }
}
