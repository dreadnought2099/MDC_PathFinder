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
     * Called when creating a brand-new ENTRANCE POINT (via create form).
     *
     * Behavior:
     * - Creates a new Room with type = 'entrance_point'
     * - Automatically connects it to ALL existing regular rooms (ONE-WAY ONLY)
     * - Path direction: entrance_point -> regular_room
     * - Skips connecting to other entrances
     * - Uses firstOrCreate to avoid duplicate paths
     */
    public function createEntrancePoint(array $roomData)
    {
        return DB::transaction(function () use ($roomData) {
            $entrancePoint = Room::create(array_merge($roomData, [
                'room_type' => 'entrance_point'
            ]));

            $regularRooms = Room::where('room_type', 'regular')
                ->where('id', '!=', $entrancePoint->id)
                ->get();

            $pathsCreated = 0;

            foreach ($regularRooms as $room) {
                // Only create ONE-WAY path: entrance -> regular room
                $path = Path::firstOrCreate([
                    'from_room_id' => $entrancePoint->id,
                    'to_room_id'   => $room->id,
                ]);

                if ($path->wasRecentlyCreated) $pathsCreated++;
            }

            Log::info("Created entrance gate '{$entrancePoint->name}' with {$pathsCreated} new one-way paths");

            return [
                'room'            => $entrancePoint,
                'paths_created'   => $pathsCreated,
                'rooms_connected' => $regularRooms->count()
            ];
        });
    }

    /**
     * Scenario:
     * Called when creating a brand-new REGULAR ROOM.
     *
     * Behavior:
     * - Connects ALL existing ENTRANCE POINTS to this new room (ONE-WAY ONLY)
     * - Path direction: entrance_point -> new_regular_room
     * - Does NOT create paths between regular rooms
     * - Uses firstOrCreate so restored/old paths are reused, no duplicates
     */
    public function connectNewRoomToAllRooms(Room $newRoom)
    {
        // Only connect entrance points to this new regular room
        $entrancePoints = Room::where('room_type', 'entrance_point')
            ->where('id', '!=', $newRoom->id)
            ->get();

        $pathsCreated = 0;

        foreach ($entrancePoints as $entrance) {
            // Only create ONE-WAY path: entrance -> new room
            $path = Path::firstOrCreate([
                'from_room_id' => $entrance->id,
                'to_room_id'   => $newRoom->id,
            ]);

            if ($path->wasRecentlyCreated) $pathsCreated++;
        }

        return [
            'paths_created'   => $pathsCreated,
            'rooms_connected' => $entrancePoints->count(),
        ];
    }

    /**
     * Scenario:
     * Called when REMOVING or DEMOTING an entrance point.
     *
     * Behavior:
     * - Deletes all paths originating from this entrance point
     * - Leaves the room record intact but isolated
     */
    public function removeEntrancePointPaths(Room $entrancePoint)
    {
        if ($entrancePoint->room_type !== 'entrance_point') {
            return;
        }

        // Only delete paths FROM this entrance point
        Path::where('from_room_id', $entrancePoint->id)->delete();
    }

    /**
     * Scenario:
     * Called when converting a REGULAR ROOM into an ENTRANCE (via update/restore).
     *
     * Behavior:
     * - Connects the entrance to ALL regular rooms (ONE-WAY ONLY)
     * - Path direction: new_entrance -> regular_room
     * - Uses firstOrCreate so old/restored paths are reused, missing ones get added
     */
    public function reconnectEntrancePoint(Room $room)
    {
        // Only connect to regular rooms
        $regularRooms = Room::where('room_type', 'regular')
            ->where('id', '!=', $room->id)
            ->get();

        $pathsCreated = 0;

        foreach ($regularRooms as $regularRoom) {
            // Only create ONE-WAY path: entrance -> regular room
            $path = Path::firstOrCreate([
                'from_room_id' => $room->id,
                'to_room_id'   => $regularRoom->id,
            ]);

            if ($path->wasRecentlyCreated) $pathsCreated++;
        }

        return $pathsCreated;
    }
}