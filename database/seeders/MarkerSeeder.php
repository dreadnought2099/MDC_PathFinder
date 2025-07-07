<?php

namespace Database\Seeders;

use App\Models\Marker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Marker::create([
            'marker_id' => 'marker1',
            'pattern_url' => 'marker-patterns/pattern-1.patt',
        ]);

        Marker::create([
            'marker_id' => 'marker2',
            'pattern_url' => 'marker-patterns/pattern-2.patt',
        ]);
    }
}
