<?php

namespace Database\Factories;

use App\Models\OfficeHour;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'name' => ucfirst($name),
            'description' => $this->faker->paragraph,
            'image_path' => null,
            'video_path' => null,
            'marker_id' => null,
            'qr_code_path' => null,
        ];
    }

    public function withMedia()
    {
        return $this->state(function () {
            return [
                'image_path' => 'rooms/' . $this->faker->randomNumber() . '/cover_images/' . Str::random(10) . '.jpg',
                'video_path' => 'rooms/' . $this->faker->randomNumber() . '/videos/' . Str::random(10) . '.mp4',
            ];
        });
    }

    public function withMarker()
    {
        return $this->state(function () {
            $id = $this->faker->unique()->numberBetween(1, 9999);
            $markerId = 'room_' . $id;

            return [
                'marker_id' => $markerId,
                'qr_code_path' => "rooms/{$id}/qrcodes/{$markerId}.svg",
            ];
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (Room $room) {
            // Attach carousel images
            RoomImage::factory()->count(3)->create([
                'room_id' => $room->id,
            ]);

            // Attach office hours (e.g., 3 random days)
            $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $randomDays = $this->faker->randomElements($days, 3);

            foreach ($randomDays as $day) {
                OfficeHour::factory()->create([
                    'room_id' => $room->id,
                    'day' => $day,
                ]);
            }
        });
    }
}
