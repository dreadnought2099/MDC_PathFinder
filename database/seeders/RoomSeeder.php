<?php

namespace Database\Seeders;

use App\Models\Marker;
use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marker1 = Marker::where('marker_id', 'marker-1')->first();
        $marker2 = Marker::where('marker_id', 'marker-2')->first();

        Room::create([
            'name' => 'Library',
            'marker_id' => $marker1->id,
        ]);

        Room::create([
            'name' => 'Computer Lab',
            'marker_id' => $marker2->id,
        ]);
    }
}
