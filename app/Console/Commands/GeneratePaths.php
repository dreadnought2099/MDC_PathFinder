<?php

namespace App\Console\Commands;

use App\Models\Path;
use App\Models\Room;
use Illuminate\Console\Command;

class GeneratePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paths:generate 
                            {--force : Re-generate paths even if they already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing paths between all rooms (both directions)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rooms = Room::all();
        $count = 0;

        foreach ($rooms as $roomA) {
            foreach ($rooms as $roomB) {
                if ($roomA->id !== $roomB->id) {
                    if ($this->option('force')) {
                        // Delete existing and recreate if force is used
                        Path::updateOrCreate([
                            'from_room_id' => $roomA->id,
                            'to_room_id'   => $roomB->id,
                        ]);
                        $count++;
                    } else {
                        // Create only if missing
                        $created = Path::firstOrCreate([
                            'from_room_id' => $roomA->id,
                            'to_room_id'   => $roomB->id,
                        ]);

                        if ($created->wasRecentlyCreated) {
                            $count++;
                        }
                    }
                }
            }
        }

        $this->info("Generated {$count} paths successfully.");
        return 0;
    }
}
