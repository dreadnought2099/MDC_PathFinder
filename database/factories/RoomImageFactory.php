<?php

namespace Database\Factories;

use App\Models\RoomImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomImage>
 */
class RoomImageFactory extends Factory
{
    protected $model = RoomImage::class;

    public function definition(): array
    {
        return [
            'image_path' => 'rooms/' . $this->faker->randomNumber() . '/carousel/' . Str::random(10) . '.jpg',
        ];
    }
}
