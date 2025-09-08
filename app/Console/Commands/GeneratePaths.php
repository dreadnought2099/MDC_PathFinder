<?php

namespace App\Console\Commands;

use App\Models\Path;
use App\Models\Room;
use App\Services\EntrancePointService;
use Illuminate\Console\Command;

class GeneratePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paths:generate 
                            {--force : Re-generate paths even if they already exist}
                            {--entrance-points-only : Only generate paths for entrance points}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing paths between rooms following entrance point rules';
    protected $entrancePointService;


    public function __construct(EntrancePointService $entrancePointService)
    {
        parent::__construct();
        $this->entrancePointService = $entrancePointService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $entranceGatesOnly = $this->option('entrance-points-only');

        if ($force) {
            $this->warn('Force option will delete and recreate ALL paths!');
            if (!$this->confirm('Are you sure you want to continue?')) {
                return 1;
            }

            // Delete all existing paths if force is used
            Path::truncate();
            $this->info('All existing paths deleted.');
        }

        $pathsCreated = 0;

        if ($entranceGatesOnly) {
            $pathsCreated = $this->generateEntrancePointPaths();
        } else {
            $pathsCreated = $this->generateAllPaths();
        }

        $this->info("Generated {$pathsCreated} paths successfully.");
        return 0;
    }

    protected function generateEntrancePointPaths(): int
    {
        $pathsCreated = 0;
        $entrancePoints = Room::where('room_type', 'entrance_point')->get();
        $regularRooms = Room::where('room_type', 'regular')->get();

        foreach ($entrancePoints as $points) {
            foreach ($regularRooms as $room) {
                // Bidirectional paths between entrance points and regular rooms
                $path1 = Path::firstOrCreate([
                    'from_room_id' => $points->id,
                    'to_room_id' => $room->id,
                ]);

                $path2 = Path::firstOrCreate([
                    'from_room_id' => $room->id,
                    'to_room_id' => $points->id,
                ]);

                if ($path1->wasRecentlyCreated) $pathsCreated++;
                if ($path2->wasRecentlyCreated) $pathsCreated++;
            }
        }

        return $pathsCreated;
    }

    protected function generateAllPaths(): int
    {
        $pathsCreated = 0;
        $rooms = Room::all();
        $entrancePoints = $rooms->where('room_type', 'point');
        $regularRooms = $rooms->where('room_type', 'regular');

        // 1. Connect entrance points to regular rooms (bidirectional)
        foreach ($entrancePoints as $point) {
            foreach ($regularRooms as $room) {
                $path1 = Path::firstOrCreate([
                    'from_room_id' => $point->id,
                    'to_room_id' => $room->id,
                ]);

                $path2 = Path::firstOrCreate([
                    'from_room_id' => $room->id,
                    'to_room_id' => $point->id,
                ]);

                if ($path1->wasRecentlyCreated) $pathsCreated++;
                if ($path2->wasRecentlyCreated) $pathsCreated++;
            }
        }

        // 2. Connect regular rooms to each other (bidirectional)
        // Only do this if you want full connectivity between regular rooms
        foreach ($regularRooms as $roomA) {
            foreach ($regularRooms as $roomB) {
                if ($roomA->id !== $roomB->id) {
                    $path = Path::firstOrCreate([
                        'from_room_id' => $roomA->id,
                        'to_room_id' => $roomB->id,
                    ]);

                    if ($path->wasRecentlyCreated) $pathsCreated++;
                }
            }
        }

        // Note: Entrance points are NOT connected to each other

        return $pathsCreated;
    }
}

//  Generate only missing entrance point connections
// php artisan paths:generate --entrance-points-only

//  Generate all missing paths (entrance points + regular room connections)  
// php artisan paths:generate

//  Force regenerate all paths (destructive)
// php artisan paths:generate --force