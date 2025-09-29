<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TenantTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_tenants_index()
    {
        $admin = User::factory()->admin()->create();
        $tenants = Tenant::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/tenants');

        $response->assertStatus(200);
        $response->assertViewIs('tenants.index');
        $response->assertViewHas('tenants');
    }

    /** @test */
    public function house_owner_can_view_their_building_tenants()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);
        
        $tenants = Tenant::factory()->count(3)->create(['building_id' => $building->id]);
        Tenant::factory()->count(2)->create(); // Other building's tenants

        $response = $this->actingAs($houseOwner)->get('/tenants');

        $response->assertStatus(200);
        $response->assertViewIs('tenants.index');
        $response->assertViewHas('tenants');
    }

    /** @test */
    public function admin_can_create_tenant()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);

        $tenantData = [
            'name' => 'Jane Smith',
            'contact' => '+1234567890',
            'email' => 'jane@example.com',
            'building_id' => $building->id,
            'flat_id' => $flat->id,
        ];

        $response = $this->actingAs($admin)->post('/tenants', $tenantData);

        $response->assertRedirect('/tenants');
        $this->assertDatabaseHas('tenants', $tenantData);
    }

    /** @test */
    public function house_owner_can_create_tenant_in_their_building()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);
        $flat = Flat::factory()->create(['building_id' => $building->id]);

        $tenantData = [
            'name' => 'Jane Smith',
            'contact' => '+1234567890',
            'email' => 'jane@example.com',
            'building_id' => $building->id,
            'flat_id' => $flat->id,
        ];

        $response = $this->actingAs($houseOwner)->post('/tenants', $tenantData);

        $response->assertRedirect('/tenants');
        $this->assertDatabaseHas('tenants', $tenantData);
    }

    /** @test */
    public function house_owner_cannot_create_tenant_in_other_building()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);
        
        $otherBuilding = Building::factory()->create();
        $otherFlat = Flat::factory()->create(['building_id' => $otherBuilding->id]);

        $tenantData = [
            'name' => 'Jane Smith',
            'contact' => '+1234567890',
            'email' => 'jane@example.com',
            'building_id' => $otherBuilding->id,
            'flat_id' => $otherFlat->id,
        ];

        $response = $this->actingAs($houseOwner)->post('/tenants', $tenantData);

        $response->assertStatus(403);
    }

    /** @test */
    public function tenant_can_be_created_without_flat_assignment()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();

        $tenantData = [
            'name' => 'Jane Smith',
            'contact' => '+1234567890',
            'email' => 'jane@example.com',
            'building_id' => $building->id,
            'flat_id' => null,
        ];

        $response = $this->actingAs($admin)->post('/tenants', $tenantData);

        $response->assertRedirect('/tenants');
        $this->assertDatabaseHas('tenants', $tenantData);
    }

    /** @test */
    public function admin_can_view_tenant_details()
    {
        $admin = User::factory()->admin()->create();
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($admin)->get("/tenants/{$tenant->id}");

        $response->assertStatus(200);
        $response->assertViewIs('tenants.show');
        $response->assertViewHas('tenant', $tenant);
    }

    /** @test */
    public function admin_can_update_tenant()
    {
        $admin = User::factory()->admin()->create();
        $tenant = Tenant::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $tenant->building_id]);

        $updateData = [
            'name' => 'Updated Name',
            'contact' => '+0987654321',
            'email' => 'updated@example.com',
            'building_id' => $tenant->building_id,
            'flat_id' => $flat->id,
        ];

        $response = $this->actingAs($admin)->put("/tenants/{$tenant->id}", $updateData);

        $response->assertRedirect("/tenants/{$tenant->id}");
        $this->assertDatabaseHas('tenants', array_merge(['id' => $tenant->id], $updateData));
    }

    /** @test */
    public function admin_can_delete_tenant()
    {
        $admin = User::factory()->admin()->create();
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($admin)->delete("/tenants/{$tenant->id}");

        $response->assertRedirect('/tenants');
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }

    /** @test */
    public function tenant_creation_requires_valid_data()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/tenants', []);

        $response->assertSessionHasErrors(['name', 'contact', 'email', 'building_id']);
    }

    /** @test */
    public function tenant_email_must_be_unique_per_building()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();
        
        Tenant::factory()->create([
            'email' => 'tenant@example.com',
            'building_id' => $building->id
        ]);

        $tenantData = [
            'name' => 'Jane Smith',
            'contact' => '+1234567890',
            'email' => 'tenant@example.com',
            'building_id' => $building->id,
        ];

        $response = $this->actingAs($admin)->post('/tenants', $tenantData);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function tenant_can_have_same_email_in_different_buildings()
    {
        $admin = User::factory()->admin()->create();
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();
        
        Tenant::factory()->create([
            'email' => 'tenant@example.com',
            'building_id' => $building1->id
        ]);

        $tenantData = [
            'name' => 'Jane Smith',
            'contact' => '+1234567890',
            'email' => 'tenant@example.com',
            'building_id' => $building2->id,
        ];

        $response = $this->actingAs($admin)->post('/tenants', $tenantData);

        $response->assertRedirect('/tenants');
        $this->assertDatabaseHas('tenants', $tenantData);
    }

    /** @test */
    public function tenant_cannot_be_assigned_to_flat_from_different_building()
    {
        $admin = User::factory()->admin()->create();
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);

        $tenantData = [
            'name' => 'Jane Smith',
            'contact' => '+1234567890',
            'email' => 'jane@example.com',
            'building_id' => $building1->id,
            'flat_id' => $flat2->id,
        ];

        $response = $this->actingAs($admin)->post('/tenants', $tenantData);

        $response->assertSessionHasErrors(['flat_id']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_tenants()
    {
        $response = $this->get('/tenants');

        $response->assertRedirect('/login');
    }
}



