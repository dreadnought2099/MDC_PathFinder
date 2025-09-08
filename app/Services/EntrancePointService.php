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
     * - Automatically connects it to ALL existing regular rooms
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
                $path1 = Path::firstOrCreate([
                    'from_room_id' => $entrancePoint->id,
                    'to_room_id'   => $room->id,
                ]);

                $path2 = Path::firstOrCreate([
                    'from_room_id' => $room->id,
                    'to_room_id'   => $entrancePoint->id,
                ]);

                if ($path1->wasRecentlyCreated) $pathsCreated++;
                if ($path2->wasRecentlyCreated) $pathsCreated++;
            }

            Log::info("Created entrance gate '{$entrancePoint->name}' with {$pathsCreated} new paths");

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
     * - Connects the new room to ALL existing rooms (both entrances and regulars)
     * - Ensures bidirectional paths exist
     * - Uses firstOrCreate so restored/old paths are reused, no duplicates
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
     * Called when REMOVING or DEMOTING an entrance point.
     *
     * Behavior:
     * - Deletes all paths linked to this entrance point
     * - Leaves the room record intact but isolated
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
     * Called when converting a REGULAR ROOM into an ENTRANCE (via update/restore).
     *
     * Behavior:
     * - Connects the entrance to ALL other rooms (regular + entrances)
     * - Ensures bidirectional paths
     * - Uses firstOrCreate so old/restored paths are reused, missing ones get added
     */
    public function reconnectEntrancePoint(Room $room)
    {
        $rooms = Room::where('id', '!=', $room->id)->get();
        $pathsCreated = 0;

        foreach ($rooms as $other) {
            $path1 = Path::firstOrCreate([
                'from_room_id' => $room->id,
                'to_room_id'   => $other->id,
            ]);

            $path2 = Path::firstOrCreate([
                'from_room_id' => $other->id,
                'to_room_id'   => $room->id,
            ]);

            if ($path1->wasRecentlyCreated) $pathsCreated++;
            if ($path2->wasRecentlyCreated) $pathsCreated++;
        }

        return $pathsCreated;
    }
}