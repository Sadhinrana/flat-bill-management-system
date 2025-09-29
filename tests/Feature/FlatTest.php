<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FlatTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_flats_index()
    {
        $admin = User::factory()->admin()->create();
        $flats = Flat::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/flats');

        $response->assertStatus(200);
        $response->assertViewIs('flats.index');
        $response->assertViewHas('flats');
    }

    /** @test */
    public function house_owner_can_view_their_building_flats()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);

        $flats = Flat::factory()->count(3)->create(['building_id' => $building->id]);
        Flat::factory()->count(2)->create(); // Other building's flats

        $response = $this->actingAs($houseOwner)->get('/flats');

        $response->assertStatus(200);
        $response->assertViewIs('flats.index');
        $response->assertViewHas('flats');
    }

    /** @test */
    public function admin_can_create_flat()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();

        $flatData = [
            'flat_number' => 'A101',
            'owner_name' => 'John Doe',
            'owner_contact' => '+1234567890',
            'owner_email' => 'john@example.com',
            'building_id' => $building->id,
        ];

        $response = $this->actingAs($admin)->post('/flats', $flatData);

        $response->assertRedirect('/flats');
        $this->assertDatabaseHas('flats', $flatData);
    }

    /** @test */
    public function house_owner_can_create_flat_in_their_building()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);

        $flatData = [
            'flat_number' => 'A101',
            'owner_name' => 'John Doe',
            'owner_contact' => '+1234567890',
            'owner_email' => 'john@example.com',
            'building_id' => $building->id,
        ];

        $response = $this->actingAs($houseOwner)->post('/flats', $flatData);

        $response->assertRedirect('/flats');
        $this->assertDatabaseHas('flats', $flatData);
    }

    /** @test */
    public function house_owner_cannot_create_flat_in_other_building()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);

        $otherBuilding = Building::factory()->create();

        $flatData = [
            'flat_number' => 'A101',
            'owner_name' => 'John Doe',
            'owner_contact' => '+1234567890',
            'owner_email' => 'john@example.com',
            'building_id' => $otherBuilding->id,
        ];

        $response = $this->actingAs($houseOwner)->post('/flats', $flatData);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_flat_details()
    {
        $admin = User::factory()->admin()->create();
        $flat = Flat::factory()->create();

        $response = $this->actingAs($admin)->get("/flats/{$flat->id}");

        $response->assertStatus(200);
        $response->assertViewIs('flats.show');
        $response->assertViewHas('flat', $flat);
    }

    /** @test */
    public function house_owner_can_view_their_building_flat_details()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);
        $flat = Flat::factory()->create(['building_id' => $building->id]);

        $response = $this->actingAs($houseOwner)->get("/flats/{$flat->id}");

        $response->assertStatus(200);
        $response->assertViewIs('flats.show');
        $response->assertViewHas('flat', $flat);
    }

    /** @test */
    public function house_owner_cannot_view_other_building_flat_details()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);

        $otherFlat = Flat::factory()->create();

        $response = $this->actingAs($houseOwner)->get("/flats/{$otherFlat->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_flat()
    {
        $admin = User::factory()->admin()->create();
        $flat = Flat::factory()->create();

        $updateData = [
            'flat_number' => 'B202',
            'owner_name' => 'Jane Smith',
            'owner_contact' => '+0987654321',
            'owner_email' => 'jane@example.com',
            'building_id' => $flat->building_id,
        ];

        $response = $this->actingAs($admin)->put("/flats/{$flat->id}", $updateData);

        $response->assertRedirect("/flats");
        $this->assertDatabaseHas('flats', array_merge(['id' => $flat->id], $updateData));
    }

    /** @test */
    public function admin_can_delete_flat()
    {
        $admin = User::factory()->admin()->create();
        $flat = Flat::factory()->create();

        $response = $this->actingAs($admin)->delete("/flats/{$flat->id}");

        $response->assertRedirect('/flats');
        $this->assertDatabaseMissing('flats', ['id' => $flat->id]);
    }

    /** @test */
    public function flat_creation_requires_valid_data()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/flats', []);

        $response->assertSessionHasErrors(['flat_number', 'owner_name', 'owner_contact', 'owner_email', 'building_id']);
    }

    /** @test */
    public function flat_number_must_be_unique_per_building()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();

        Flat::factory()->create([
            'flat_number' => 'A101',
            'building_id' => $building->id
        ]);

        $flatData = [
            'flat_number' => 'A101',
            'owner_name' => 'John Doe',
            'owner_contact' => '+1234567890',
            'owner_email' => 'john@example.com',
            'building_id' => $building->id,
        ];

        $response = $this->actingAs($admin)->post('/flats', $flatData);

        $response->assertSessionHasErrors(['flat_number']);
    }

    /** @test */
    public function flat_deletion_cascades_to_related_records()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);

        // Create related records
        $tenant = \App\Models\Tenant::factory()->create([
            'building_id' => $building->id,
            'flat_id' => $flat->id
        ]);
        $category = \App\Models\BillCategory::factory()->create(['building_id' => $building->id]);
        $bill = \App\Models\Bill::factory()->create([
            'building_id' => $building->id,
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id
        ]);

        $response = $this->actingAs($admin)->delete("/flats/{$flat->id}");

        $response->assertRedirect('/flats');
        $this->assertDatabaseMissing('flats', ['id' => $flat->id]);
        $this->assertDatabaseMissing('bills', ['id' => $bill->id]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_flats()
    {
        $response = $this->get('/flats');

        $response->assertRedirect('/login');
    }
}



