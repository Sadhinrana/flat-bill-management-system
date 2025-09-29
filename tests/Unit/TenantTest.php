<?php

namespace Tests\Unit;

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
    public function it_can_create_a_tenant()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);

        $tenant = Tenant::factory()->create([
            'name' => 'Jane Smith',
            'contact' => '+1234567890',
            'email' => 'jane@example.com',
            'building_id' => $building->id,
            'flat_id' => $flat->id
        ]);

        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertEquals('Jane Smith', $tenant->name);
        $this->assertEquals('jane@example.com', $tenant->email);
        $this->assertEquals($building->id, $tenant->building_id);
        $this->assertEquals($flat->id, $tenant->flat_id);
    }

    /** @test */
    public function it_belongs_to_a_building()
    {
        $building = Building::factory()->create();
        $tenant = Tenant::factory()->create(['building_id' => $building->id]);

        $this->assertInstanceOf(Building::class, $tenant->building);
        $this->assertEquals($building->id, $tenant->building->id);
    }

    /** @test */
    public function it_can_belong_to_a_flat()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $tenant = Tenant::factory()->create([
            'building_id' => $building->id,
            'flat_id' => $flat->id
        ]);

        $this->assertInstanceOf(Flat::class, $tenant->flat);
        $this->assertEquals($flat->id, $tenant->flat->id);
    }

    /** @test */
    public function it_can_exist_without_a_flat()
    {
        $building = Building::factory()->create();
        $tenant = Tenant::factory()->create([
            'building_id' => $building->id,
            'flat_id' => null
        ]);

        $this->assertNull($tenant->flat_id);
        $this->assertNull($tenant->flat);
    }

    /** @test */
    public function it_can_scope_tenants_for_building()
    {
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();

        $tenant1 = Tenant::factory()->create(['building_id' => $building1->id]);
        $tenant2 = Tenant::factory()->create(['building_id' => $building2->id]);

        $building1Tenants = Tenant::forBuilding($building1->id)->get();
        $building2Tenants = Tenant::forBuilding($building2->id)->get();

        $this->assertCount(1, $building1Tenants);
        $this->assertTrue($building1Tenants->contains($tenant1));
        $this->assertFalse($building1Tenants->contains($tenant2));

        $this->assertCount(1, $building2Tenants);
        $this->assertTrue($building2Tenants->contains($tenant2));
        $this->assertFalse($building2Tenants->contains($tenant1));
    }

    /** @test */
    public function it_can_scope_tenants_for_owner()
    {
        $owner1 = User::factory()->create(['role' => 'house_owner']);
        $owner2 = User::factory()->create(['role' => 'house_owner']);

        $building1 = Building::factory()->create(['owner_id' => $owner1->id]);
        $building2 = Building::factory()->create(['owner_id' => $owner2->id]);

        $tenant1 = Tenant::factory()->create(['building_id' => $building1->id]);
        $tenant2 = Tenant::factory()->create(['building_id' => $building2->id]);

        $owner1Tenants = Tenant::forOwner($owner1->id)->get();
        $owner2Tenants = Tenant::forOwner($owner2->id)->get();

        $this->assertCount(1, $owner1Tenants);
        $this->assertTrue($owner1Tenants->contains($tenant1));
        $this->assertFalse($owner1Tenants->contains($tenant2));

        $this->assertCount(1, $owner2Tenants);
        $this->assertTrue($owner2Tenants->contains($tenant2));
        $this->assertFalse($owner2Tenants->contains($tenant1));
    }

    /** @test */
    public function it_can_have_unique_email_per_building()
    {
        $building = Building::factory()->create();

        Tenant::factory()->create([
            'email' => 'tenant@example.com',
            'building_id' => $building->id
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Tenant::factory()->create([
            'email' => 'tenant@example.com',
            'building_id' => $building->id
        ]);
    }

    /** @test */
    public function it_can_have_same_email_in_different_buildings()
    {
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();

        $tenant1 = Tenant::factory()->create([
            'email' => 'tenant@example.com',
            'building_id' => $building1->id
        ]);

        $tenant2 = Tenant::factory()->create([
            'email' => 'tenant@example.com',
            'building_id' => $building2->id
        ]);

        $this->assertNotEquals($tenant1->id, $tenant2->id);
        $this->assertEquals('tenant@example.com', $tenant1->email);
        $this->assertEquals('tenant@example.com', $tenant2->email);
    }

    /** @test */
    public function it_can_be_assigned_to_different_flats()
    {
        $building = Building::factory()->create();
        $flat1 = Flat::factory()->create(['building_id' => $building->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building->id]);

        $tenant = Tenant::factory()->create([
            'building_id' => $building->id,
            'flat_id' => $flat1->id
        ]);

        $this->assertEquals($flat1->id, $tenant->flat_id);

        $tenant->update(['flat_id' => $flat2->id]);

        $this->assertEquals($flat2->id, $tenant->fresh()->flat_id);
    }

    /** @test */
    public function it_can_be_unassigned_from_flat()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);

        $tenant = Tenant::factory()->create([
            'building_id' => $building->id,
            'flat_id' => $flat->id
        ]);

        $this->assertEquals($flat->id, $tenant->flat_id);

        $tenant->update(['flat_id' => null]);

        $this->assertNull($tenant->fresh()->flat_id);
    }

    /** @test */
    /*public function it_cannot_be_assigned_to_flat_from_different_building()
    {
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);

        $tenant = Tenant::factory()->create([
            'building_id' => $building1->id,
            'flat_id' => null
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $tenant->update(['flat_id' => $flat2->id]);
    }*/
}



