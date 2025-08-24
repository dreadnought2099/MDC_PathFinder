<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;


        return [
            'room_id'     => Room::factory(), // automatically link staff to a room
            'first_name'  => $firstName,
            'middle_name' => $this->faker->optional()->firstName,
            'last_name'   => $lastName,
            'suffix'      => $this->faker->optional()->suffix,
            'credentials' => $this->faker->optional()->jobTitle,
            'position'    => $this->faker->jobTitle,
            'bio'         => $this->faker->optional()->paragraph,
            'email'       => $this->faker->unique()->safeEmail,
            'phone_num'   => $this->faker->optional()->phoneNumber,
            'photo_path'  => null, // normally handled after upload
        ];
    }

    /**
     * State for staff with fake photo paths
     */
    public function withPhoto()
    {
        return $this->state(function () {
            return [
                'photo_path' => 'staffs/' . $this->faker->randomNumber() . '/' . Str::random(10) . '.jpg',
            ];
        });
    }
}
