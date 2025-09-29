<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuildingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_buildings_index()
    {
        $admin = User::factory()->admin()->create();
        $buildings = Building::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/buildings');

        $response->assertStatus(200);
        $response->assertViewIs('buildings.index');
        $response->assertViewHas('buildings');
    }

    /** @test */
    public function house_owner_cannot_view_buildings_index()
    {
        $houseOwner = User::factory()->houseOwner()->create();

        $response = $this->actingAs($houseOwner)->get('/buildings');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_building()
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->houseOwner()->create();

        $buildingData = [
            'name' => 'Sunset Apartments',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'owner_id' => $owner->id,
        ];

        $response = $this->actingAs($admin)->post('/buildings', $buildingData);

        $response->assertRedirect('/buildings');
        $this->assertDatabaseHas('buildings', $buildingData);
    }

    /** @test */
    public function admin_can_view_building_details()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();

        $response = $this->actingAs($admin)->get("/buildings/{$building->id}");

        $response->assertStatus(200);
        $response->assertViewIs('buildings.show');
        $response->assertViewHas('building', $building);
    }

    /** @test */
    public function admin_can_update_building()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();
        $newOwner = User::factory()->houseOwner()->create();

        $updateData = [
            'name' => 'Updated Building Name',
            'address' => '456 New St',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'postal_code' => '90210',
            'owner_id' => $newOwner->id,
        ];

        $response = $this->actingAs($admin)->put("/buildings/{$building->id}", $updateData);

        $response->assertRedirect("/buildings");
        $this->assertDatabaseHas('buildings', array_merge(['id' => $building->id], $updateData));
    }

    /** @test */
    public function admin_can_delete_building()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();

        $response = $this->actingAs($admin)->delete("/buildings/{$building->id}");

        $response->assertRedirect('/buildings');
        $this->assertDatabaseMissing('buildings', ['id' => $building->id]);
    }

    /** @test */
    public function building_creation_requires_valid_data()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/buildings', []);

        $response->assertSessionHasErrors(['name', 'address', 'city', 'state', 'postal_code', 'owner_id']);
    }

    /** @test */
    public function building_update_requires_valid_data()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();

        $response = $this->actingAs($admin)->put("/buildings/{$building->id}", []);

        $response->assertSessionHasErrors(['name', 'address', 'city', 'state', 'postal_code', 'owner_id']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_buildings()
    {
        $response = $this->get('/buildings');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function building_deletion_cascades_to_related_records()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();

        // Create related records
        $flat = \App\Models\Flat::factory()->create(['building_id' => $building->id]);
        $tenant = \App\Models\Tenant::factory()->create(['building_id' => $building->id]);
        $category = \App\Models\BillCategory::factory()->create(['building_id' => $building->id]);
        $bill = \App\Models\Bill::factory()->create([
            'building_id' => $building->id,
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id
        ]);

        $response = $this->actingAs($admin)->delete("/buildings/{$building->id}");

        $response->assertRedirect('/buildings');
        $this->assertDatabaseMissing('buildings', ['id' => $building->id]);
        $this->assertDatabaseMissing('flats', ['id' => $flat->id]);
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
        $this->assertDatabaseMissing('bill_categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('bills', ['id' => $bill->id]);
    }
}



