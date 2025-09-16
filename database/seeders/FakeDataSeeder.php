<?php

namespace Database\Seeders;

use App\Models\OfficeHour;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\Staff;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 Rooms
        Room::factory()
            ->withMedia()
            ->withMarker()
            ->count(10)
            ->create()
            ->each(function (Room $room) {
                // Add 3 carousel images
                RoomImage::factory()->count(3)->create([
                    'room_id' => $room->id,
                ]);

                // Add office hours for all 7 days
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                foreach ($days as $day) {
                    OfficeHour::factory()->create([
                        'room_id' => $room->id,
                        'day'     => $day,
                    ]);
                }

                // Add 2–5 staff members per room
                Staff::factory()
                    ->count(rand(2, 5))
                    ->create([
                        'room_id' => $room->id,
                    ]);
            });
    }
}


//            Usage

// Normal seeding (local/dev)   ---- php artisan db:seed  → runs AdminSeeder + FakeDataSeeder.

// Production seeding   ------ php artisan db:seed --force → runs only AdminSeeder.


// Manually add fake data (even in production, e.g. demo site) ------ php artisan db:seed --class=FakeDataSeeder --force

// This way, production is always safe, and you still have a way to load demo data whenever needed.