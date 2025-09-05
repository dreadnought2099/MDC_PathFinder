<?php

namespace App\Console\Commands;

use App\Models\Path;
use App\Models\Room;
use App\Services\EntranceGateService;
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
                            {--entrance-gates-only : Only generate paths for entrance gates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing paths between rooms following entrance gate rules';
    protected $entranceGateService;


    public function __construct(EntranceGateService $entranceGateService)
    {
        parent::__construct();
        $this->entranceGateService = $entranceGateService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $entranceGatesOnly = $this->option('entrance-gates-only');

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
            $pathsCreated = $this->generateEntranceGatePaths();
        } else {
            $pathsCreated = $this->generateAllPaths();
        }

        $this->info("Generated {$pathsCreated} paths successfully.");
        return 0;
    }

    protected function generateEntranceGatePaths(): int
    {
        $pathsCreated = 0;
        $entranceGates = Room::where('room_type', 'entrance_gate')->get();
        $regularRooms = Room::where('room_type', 'regular')->get();

        foreach ($entranceGates as $gate) {
            foreach ($regularRooms as $room) {
                // Bidirectional paths between entrance gates and regular rooms
                $path1 = Path::firstOrCreate([
                    'from_room_id' => $gate->id,
                    'to_room_id' => $room->id,
                ]);

                $path2 = Path::firstOrCreate([
                    'from_room_id' => $room->id,
                    'to_room_id' => $gate->id,
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
        $entranceGates = $rooms->where('room_type', 'entrance_gate');
        $regularRooms = $rooms->where('room_type', 'regular');

        // 1. Connect entrance gates to regular rooms (bidirectional)
        foreach ($entranceGates as $gate) {
            foreach ($regularRooms as $room) {
                $path1 = Path::firstOrCreate([
                    'from_room_id' => $gate->id,
                    'to_room_id' => $room->id,
                ]);

                $path2 = Path::firstOrCreate([
                    'from_room_id' => $room->id,
                    'to_room_id' => $gate->id,
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

        // Note: Entrance gates are NOT connected to each other

        return $pathsCreated;
    }
}

//  Generate only missing entrance gate connections
// php artisan paths:generate --entrance-gates-only

//  Generate all missing paths (entrance gates + regular room connections)  
// php artisan paths:generate

//  Force regenerate all paths (destructive)
// php artisan paths:generate --force