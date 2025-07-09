<?php

namespace Database\Seeders;

use App\Models\Marker;
use App\Models\Path;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marker1 = Marker::where('marker_id', 'marker-1')->first();
        $marker2 = Marker::where('marker_id', 'marker-2')->first();

        Path::create([
            'from_marker_id' => $marker1->id,
            'to_marker_id' => $marker2->id,
            'angle' => 90, // example: facing east
        ]);
    }
}
