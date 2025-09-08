<?php

namespace App\Services;

use App\Models\Path;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntrancePointService
{
    /**
     * Scenario:
     * Used when creating a brand-new entrance point (from create form).
     * - Creates the room with type = 'entrance_point'
     * - Automatically connects it to ALL existing *regular* rooms
     * - Skips connecting to other entrances
     */
    public function createEntrancePoint(array $roomData)
    {
        return DB::transaction(function () use ($roomData) {
            // Create the entrance gate room
            $entrancePoint = Room::create(array_merge($roomData, [
                'room_type' => 'entrance_point'
            ]));

            // Get all regular rooms (excluding other entrance gates)
            $regularRooms = Room::where('room_type', 'regular')
                ->where('id', '!=', $entrancePoint->id)
                ->get();

            $pathsCreated = 0;

            // Create bidirectional paths between entrance gate and all regular rooms
            foreach ($regularRooms as $room) {
                $path1 = Path::firstOrCreate([
                    'from_room_id' => $entrancePoint->id,
                    'to_room_id' => $room->id,
                ]);

                $path2 = Path::firstOrCreate([
                    'from_room_id' => $room->id,
                    'to_room_id' => $entrancePoint->id,
                ]);

                if ($path1->wasRecentlyCreated) $pathsCreated++;
                if ($path2->wasRecentlyCreated) $pathsCreated++;
            }

            Log::info("Created entrance gate '{$entrancePoint->name}' with {$pathsCreated} new paths");

            return [
                'room' => $entrancePoint,
                'paths_created' => $pathsCreated,
                'rooms_connected' => $regularRooms->count()
            ];
        });
    }

    /**
     * Scenario:
     * Used when creating a *new regular room* (not an entrance).
     * - Connects this new room to all existing rooms (entrances + regulars).
     * - Ensures bidirectional paths exist.
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
     * Scenario:
     * Used when deleting or demoting an entrance point.
     * - Clears ALL paths connected to this entrance.
     * - Keeps the room record but makes it isolated until reconnected.
     */
    public function removeEntrancePointPaths(Room $entrancePoint)
    {
        if ($entrancePoint->room_type !== 'entrance_point') {
            return;
        }

        Path::where('from_room_id', $entrancePoint->id)
            ->orWhere('to_room_id', $entrancePoint->id)
            ->delete();
    }

    /**
     * Scenario:
     * Used when *converting an existing room* into an entrance (via update).
     * - Removes its old paths in the controller
     * - Reconnects it to *all other rooms* (regular + entrances)
     * - Ensures bidirectional paths
     */
    public function reconnectEntrancePoint(Room $room)
    {
        $rooms = Room::where('id', '!=', $room->id)->get();

        foreach ($rooms as $other) {
            Path::create([
                'from_room_id' => $room->id,
                'to_room_id'   => $other->id,
            ]);
            Path::create([
                'from_room_id' => $other->id,
                'to_room_id'   => $room->id,
            ]);
        }
    }
}