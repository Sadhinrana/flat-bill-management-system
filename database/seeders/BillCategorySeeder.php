<?php

namespace Database\Seeders;

use App\Models\BillCategory;
use App\Models\Building;
use Illuminate\Database\Seeder;

class BillCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = Building::all();
        $categories = ['Electricity', 'Gas Bill', 'Water Bill', 'Utility Charges'];

        foreach ($buildings as $building) {
            foreach ($categories as $category) {
                BillCategory::create([
                    'name' => $category,
                    'description' => 'Monthly ' . strtolower($category) . ' charges',
                    'building_id' => $building->id,
                ]);
            }
        }
    }
}
