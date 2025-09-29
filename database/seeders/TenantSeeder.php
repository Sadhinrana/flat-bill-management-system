<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Building;
use App\Models\Flat;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = Building::all();

        foreach ($buildings as $building) {
            $flats = $building->flats;
            
            // Create 2-3 tenants per building
            for ($i = 1; $i <= 3; $i++) {
                $flat = $flats->random();
                
                Tenant::create([
                    'name' => 'Tenant ' . $i,
                    'contact' => '+1-555-100' . $i,
                    'email' => 'tenant' . $i . '@example.com',
                    'building_id' => $building->id,
                    'flat_id' => $flat->id,
                ]);
            }
        }
    }
}
