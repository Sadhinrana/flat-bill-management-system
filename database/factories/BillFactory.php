<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Flat;
use App\Models\BillCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bill>
 */
class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 50, 500);
        $status = fake()->randomElement(['paid', 'unpaid']);
        
        return [
            'month' => fake()->date('Y-m'),
            'amount' => $amount,
            'due_amount' => $status === 'unpaid' ? $amount : 0.00,
            'status' => $status,
            'notes' => fake()->optional()->sentence(),
            'flat_id' => Flat::factory(),
            'bill_category_id' => BillCategory::factory(),
            'building_id' => Building::factory(),
        ];
    }

    /**
     * Create a paid bill.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'due_amount' => 0.00,
        ]);
    }

    /**
     * Create an unpaid bill.
     */
    public function unpaid(): static
    {
        return $this->state(function (array $attributes) {
            $amount = $attributes['amount'] ?? fake()->randomFloat(2, 50, 500);
            return [
                'status' => 'unpaid',
                'due_amount' => $amount,
            ];
        });
    }
}



