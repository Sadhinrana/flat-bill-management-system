<?php

namespace Database\Factories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BillCategory>
 */
class BillCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Electricity', 'Gas', 'Water', 'Utility Charges', 'Maintenance', 'Security']),
            'description' => fake()->sentence(),
            'building_id' => Building::factory(),
        ];
    }
}



