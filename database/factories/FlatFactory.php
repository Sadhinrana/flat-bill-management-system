<?php

namespace Database\Factories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flat>
 */
class FlatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'flat_number' => fake()->randomElement(['A', 'B', 'C']) . fake()->numberBetween(101, 999),
            'owner_name' => fake()->name(),
            'owner_contact' => fake()->phoneNumber(),
            'owner_email' => fake()->safeEmail(),
            'building_id' => Building::factory(),
        ];
    }
}



