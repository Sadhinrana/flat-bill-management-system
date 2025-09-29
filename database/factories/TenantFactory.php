<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Flat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'contact' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'building_id' => Building::factory(),
            'flat_id' => null,
        ];
    }

    /**
     * Create a tenant assigned to a specific flat.
     */
    public function assignedToFlat(Flat $flat): static
    {
        return $this->state(fn (array $attributes) => [
            'building_id' => $flat->building_id,
            'flat_id' => $flat->id,
        ]);
    }
}



