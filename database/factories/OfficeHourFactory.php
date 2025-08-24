<?php

namespace Database\Factories;

use App\Models\OfficeHour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfficeHour>
 */
class OfficeHourFactory extends Factory
{
    protected $model = OfficeHour::class;

    public function definition(): array
    {
        return [
            'day' => $this->faker->randomElement(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i'),
        ];
    }
}
