<?php

namespace Database\Seeders;

use App\Models\Flat;
use App\Models\Building;
use Illuminate\Database\Seeder;

class FlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = Building::all();

        foreach ($buildings as $building) {
            // Create 5 flats for each building
            for ($i = 1; $i <= 5; $i++) {
                Flat::create([
                    'flat_number' => 'A' . $i,
                    'owner_name' => 'Flat Owner ' . $i,
                    'owner_contact' => '+1-555-000' . $i,
                    'owner_email' => 'flat' . $i . '@example.com',
                    'building_id' => $building->id,
                ]);
            }
        }
    }
}
