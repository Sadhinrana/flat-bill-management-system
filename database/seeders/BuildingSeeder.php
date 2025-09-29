<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\User;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $houseOwners = User::where('role', 'house_owner')->get();

        // Create buildings for each house owner
        foreach ($houseOwners as $index => $owner) {
            Building::create([
                'name' => 'Building ' . ($index + 1),
                'address' => '123 Main Street, Apt ' . ($index + 1),
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '1000' . ($index + 1),
                'owner_id' => $owner->id,
            ]);
        }
    }
}
