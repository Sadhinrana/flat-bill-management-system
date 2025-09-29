<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Tenant;
use App\Models\BillCategory;
use App\Models\Bill;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MultiTenantTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function house_owner_can_only_access_their_building_data()
    {
        // Create two house owners with their buildings
        $owner1 = User::factory()->houseOwner()->create();
        $owner2 = User::factory()->houseOwner()->create();
        
        $building1 = Building::factory()->create(['owner_id' => $owner1->id]);
        $building2 = Building::factory()->create(['owner_id' => $owner2->id]);
        
        $owner1->update(['building_id' => $building1->id]);
        $owner2->update(['building_id' => $building2->id]);
        
        // Create flats for each building
        $flat1 = Flat::factory()->create(['building_id' => $building1->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);
        
        // Create tenants for each building
        $tenant1 = Tenant::factory()->create(['building_id' => $building1->id]);
        $tenant2 = Tenant::factory()->create(['building_id' => $building2->id]);
        
        // Create bill categories for each building
        $category1 = BillCategory::factory()->create(['building_id' => $building1->id]);
        $category2 = BillCategory::factory()->create(['building_id' => $building2->id]);
        
        // Create bills for each building
        $bill1 = Bill::factory()->create([
            'building_id' => $building1->id,
            'flat_id' => $flat1->id,
            'bill_category_id' => $category1->id
        ]);
        $bill2 = Bill::factory()->create([
            'building_id' => $building2->id,
            'flat_id' => $flat2->id,
            'bill_category_id' => $category2->id
        ]);

        // Test owner1 can only access their building's data
        $this->actingAs($owner1);
        
        // Can access their building's flat
        $response = $this->get("/flats/{$flat1->id}");
        $response->assertStatus(200);
        
        // Cannot access other building's flat
        $response = $this->get("/flats/{$flat2->id}");
        $response->assertStatus(403);
        
        // Can access their building's tenant
        $response = $this->get("/tenants/{$tenant1->id}");
        $response->assertStatus(200);
        
        // Cannot access other building's tenant
        $response = $this->get("/tenants/{$tenant2->id}");
        $response->assertStatus(403);
        
        // Can access their building's bill
        $response = $this->get("/bills/{$bill1->id}");
        $response->assertStatus(200);
        
        // Cannot access other building's bill
        $response = $this->get("/bills/{$bill2->id}");
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_all_building_data()
    {
        // Create admin and two house owners with their buildings
        $admin = User::factory()->admin()->create();
        $owner1 = User::factory()->houseOwner()->create();
        $owner2 = User::factory()->houseOwner()->create();
        
        $building1 = Building::factory()->create(['owner_id' => $owner1->id]);
        $building2 = Building::factory()->create(['owner_id' => $owner2->id]);
        
        // Create flats for each building
        $flat1 = Flat::factory()->create(['building_id' => $building1->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);
        
        // Create tenants for each building
        $tenant1 = Tenant::factory()->create(['building_id' => $building1->id]);
        $tenant2 = Tenant::factory()->create(['building_id' => $building2->id]);
        
        // Create bill categories for each building
        $category1 = BillCategory::factory()->create(['building_id' => $building1->id]);
        $category2 = BillCategory::factory()->create(['building_id' => $building2->id]);
        
        // Create bills for each building
        $bill1 = Bill::factory()->create([
            'building_id' => $building1->id,
            'flat_id' => $flat1->id,
            'bill_category_id' => $category1->id
        ]);
        $bill2 = Bill::factory()->create([
            'building_id' => $building2->id,
            'flat_id' => $flat2->id,
            'bill_category_id' => $category2->id
        ]);

        // Test admin can access all data
        $this->actingAs($admin);
        
        // Can access both buildings' flats
        $response = $this->get("/flats/{$flat1->id}");
        $response->assertStatus(200);
        
        $response = $this->get("/flats/{$flat2->id}");
        $response->assertStatus(200);
        
        // Can access both buildings' tenants
        $response = $this->get("/tenants/{$tenant1->id}");
        $response->assertStatus(200);
        
        $response = $this->get("/tenants/{$tenant2->id}");
        $response->assertStatus(200);
        
        // Can access both buildings' bills
        $response = $this->get("/bills/{$bill1->id}");
        $response->assertStatus(200);
        
        $response = $this->get("/bills/{$bill2->id}");
        $response->assertStatus(200);
    }

    /** @test */
    public function multi_tenant_scopes_work_correctly()
    {
        // Create two house owners with their buildings
        $owner1 = User::factory()->houseOwner()->create();
        $owner2 = User::factory()->houseOwner()->create();
        
        $building1 = Building::factory()->create(['owner_id' => $owner1->id]);
        $building2 = Building::factory()->create(['owner_id' => $owner2->id]);
        
        // Create flats for each building
        $flat1 = Flat::factory()->create(['building_id' => $building1->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);
        
        // Create tenants for each building
        $tenant1 = Tenant::factory()->create(['building_id' => $building1->id]);
        $tenant2 = Tenant::factory()->create(['building_id' => $building2->id]);
        
        // Create bill categories for each building
        $category1 = BillCategory::factory()->create(['building_id' => $building1->id]);
        $category2 = BillCategory::factory()->create(['building_id' => $building2->id]);
        
        // Create bills for each building
        $bill1 = Bill::factory()->create([
            'building_id' => $building1->id,
            'flat_id' => $flat1->id,
            'bill_category_id' => $category1->id
        ]);
        $bill2 = Bill::factory()->create([
            'building_id' => $building2->id,
            'flat_id' => $flat2->id,
            'bill_category_id' => $category2->id
        ]);

        // Test scopes work correctly
        $owner1Flats = Flat::forOwner($owner1->id)->get();
        $owner2Flats = Flat::forOwner($owner2->id)->get();
        
        $this->assertCount(1, $owner1Flats);
        $this->assertTrue($owner1Flats->contains($flat1));
        $this->assertFalse($owner1Flats->contains($flat2));
        
        $this->assertCount(1, $owner2Flats);
        $this->assertTrue($owner2Flats->contains($flat2));
        $this->assertFalse($owner2Flats->contains($flat1));
        
        $owner1Tenants = Tenant::forOwner($owner1->id)->get();
        $owner2Tenants = Tenant::forOwner($owner2->id)->get();
        
        $this->assertCount(1, $owner1Tenants);
        $this->assertTrue($owner1Tenants->contains($tenant1));
        $this->assertFalse($owner1Tenants->contains($tenant2));
        
        $this->assertCount(1, $owner2Tenants);
        $this->assertTrue($owner2Tenants->contains($tenant2));
        $this->assertFalse($owner2Tenants->contains($tenant1));
        
        $owner1Bills = Bill::forOwner($owner1->id)->get();
        $owner2Bills = Bill::forOwner($owner2->id)->get();
        
        $this->assertCount(1, $owner1Bills);
        $this->assertTrue($owner1Bills->contains($bill1));
        $this->assertFalse($owner1Bills->contains($bill2));
        
        $this->assertCount(1, $owner2Bills);
        $this->assertTrue($owner2Bills->contains($bill2));
        $this->assertFalse($owner2Bills->contains($bill1));
    }

    /** @test */
    public function data_isolation_is_maintained_across_operations()
    {
        // Create two house owners with their buildings
        $owner1 = User::factory()->houseOwner()->create();
        $owner2 = User::factory()->houseOwner()->create();
        
        $building1 = Building::factory()->create(['owner_id' => $owner1->id]);
        $building2 = Building::factory()->create(['owner_id' => $owner2->id]);
        
        $owner1->update(['building_id' => $building1->id]);
        $owner2->update(['building_id' => $building2->id]);
        
        // Create flats for each building
        $flat1 = Flat::factory()->create(['building_id' => $building1->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);
        
        // Create bill categories for each building
        $category1 = BillCategory::factory()->create(['building_id' => $building1->id]);
        $category2 = BillCategory::factory()->create(['building_id' => $building2->id]);

        // Test owner1 can only create bills for their building
        $this->actingAs($owner1);
        
        $billData1 = [
            'month' => '2024-01',
            'amount' => 150.00,
            'due_amount' => 150.00,
            'status' => 'unpaid',
            'flat_id' => $flat1->id,
            'bill_category_id' => $category1->id,
            'building_id' => $building1->id,
        ];
        
        $response = $this->post('/bills', $billData1);
        $response->assertRedirect('/bills');
        $this->assertDatabaseHas('bills', $billData1);
        
        // Test owner1 cannot create bills for other building
        $billData2 = [
            'month' => '2024-01',
            'amount' => 150.00,
            'due_amount' => 150.00,
            'status' => 'unpaid',
            'flat_id' => $flat2->id,
            'bill_category_id' => $category2->id,
            'building_id' => $building2->id,
        ];
        
        $response = $this->post('/bills', $billData2);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('bills', $billData2);
    }

    /** @test */
    public function cascade_deletion_maintains_data_isolation()
    {
        // Create two house owners with their buildings
        $owner1 = User::factory()->houseOwner()->create();
        $owner2 = User::factory()->houseOwner()->create();
        
        $building1 = Building::factory()->create(['owner_id' => $owner1->id]);
        $building2 = Building::factory()->create(['owner_id' => $owner2->id]);
        
        // Create flats for each building
        $flat1 = Flat::factory()->create(['building_id' => $building1->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);
        
        // Create tenants for each building
        $tenant1 = Tenant::factory()->create(['building_id' => $building1->id]);
        $tenant2 = Tenant::factory()->create(['building_id' => $building2->id]);
        
        // Create bill categories for each building
        $category1 = BillCategory::factory()->create(['building_id' => $building1->id]);
        $category2 = BillCategory::factory()->create(['building_id' => $building2->id]);
        
        // Create bills for each building
        $bill1 = Bill::factory()->create([
            'building_id' => $building1->id,
            'flat_id' => $flat1->id,
            'bill_category_id' => $category1->id
        ]);
        $bill2 = Bill::factory()->create([
            'building_id' => $building2->id,
            'flat_id' => $flat2->id,
            'bill_category_id' => $category2->id
        ]);

        // Delete building1
        $building1->delete();

        // Check that building1's data is deleted
        $this->assertDatabaseMissing('buildings', ['id' => $building1->id]);
        $this->assertDatabaseMissing('flats', ['id' => $flat1->id]);
        $this->assertDatabaseMissing('tenants', ['id' => $tenant1->id]);
        $this->assertDatabaseMissing('bill_categories', ['id' => $category1->id]);
        $this->assertDatabaseMissing('bills', ['id' => $bill1->id]);

        // Check that building2's data is still intact
        $this->assertDatabaseHas('buildings', ['id' => $building2->id]);
        $this->assertDatabaseHas('flats', ['id' => $flat2->id]);
        $this->assertDatabaseHas('tenants', ['id' => $tenant2->id]);
        $this->assertDatabaseHas('bill_categories', ['id' => $category2->id]);
        $this->assertDatabaseHas('bills', ['id' => $bill2->id]);
    }

    /** @test */
    public function middleware_enforces_multi_tenant_isolation()
    {
        // Create two house owners with their buildings
        $owner1 = User::factory()->houseOwner()->create();
        $owner2 = User::factory()->houseOwner()->create();
        
        $building1 = Building::factory()->create(['owner_id' => $owner1->id]);
        $building2 = Building::factory()->create(['owner_id' => $owner2->id]);
        
        $owner1->update(['building_id' => $building1->id]);
        $owner2->update(['building_id' => $building2->id]);
        
        // Create flats for each building
        $flat1 = Flat::factory()->create(['building_id' => $building1->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);

        // Test owner1 can only see their building's flats in index
        $this->actingAs($owner1);
        
        $response = $this->get('/flats');
        $response->assertStatus(200);
        
        $flats = $response->viewData('flats');
        $this->assertTrue($flats->contains($flat1));
        $this->assertFalse($flats->contains($flat2));
        
        // Test owner2 can only see their building's flats in index
        $this->actingAs($owner2);
        
        $response = $this->get('/flats');
        $response->assertStatus(200);
        
        $flats = $response->viewData('flats');
        $this->assertTrue($flats->contains($flat2));
        $this->assertFalse($flats->contains($flat1));
    }
}



