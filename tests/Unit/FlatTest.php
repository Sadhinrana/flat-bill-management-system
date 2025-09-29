<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Tenant;
use App\Models\Bill;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FlatTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_flat()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create([
            'flat_number' => 'A101',
            'owner_name' => 'John Doe',
            'owner_contact' => '+1234567890',
            'owner_email' => 'john@example.com',
            'building_id' => $building->id
        ]);

        $this->assertInstanceOf(Flat::class, $flat);
        $this->assertEquals('A101', $flat->flat_number);
        $this->assertEquals('John Doe', $flat->owner_name);
        $this->assertEquals($building->id, $flat->building_id);
    }

    /** @test */
    public function it_belongs_to_a_building()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);

        $this->assertInstanceOf(Building::class, $flat->building);
        $this->assertEquals($building->id, $flat->building->id);
    }

    /** @test */
    public function it_can_have_multiple_tenants()
    {
        $flat = Flat::factory()->create();
        $tenant1 = Tenant::factory()->create(['flat_id' => $flat->id]);
        $tenant2 = Tenant::factory()->create(['flat_id' => $flat->id]);

        $this->assertCount(2, $flat->tenants);
        $this->assertTrue($flat->tenants->contains($tenant1));
        $this->assertTrue($flat->tenants->contains($tenant2));
    }

    /** @test */
    public function it_can_have_multiple_bills()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = \App\Models\BillCategory::factory()->create(['building_id' => $building->id]);

        $bill1 = Bill::factory()->create([
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);
        $bill2 = Bill::factory()->create([
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertCount(2, $flat->bills);
        $this->assertTrue($flat->bills->contains($bill1));
        $this->assertTrue($flat->bills->contains($bill2));
    }

    /** @test */
    public function it_can_scope_flats_for_building()
    {
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();

        $flat1 = Flat::factory()->create(['building_id' => $building1->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);

        $building1Flats = Flat::forBuilding($building1->id)->get();
        $building2Flats = Flat::forBuilding($building2->id)->get();

        $this->assertCount(1, $building1Flats);
        $this->assertTrue($building1Flats->contains($flat1));
        $this->assertFalse($building1Flats->contains($flat2));

        $this->assertCount(1, $building2Flats);
        $this->assertTrue($building2Flats->contains($flat2));
        $this->assertFalse($building2Flats->contains($flat1));
    }

    /** @test */
    public function it_can_scope_flats_for_owner()
    {
        $owner1 = User::factory()->create(['role' => 'house_owner']);
        $owner2 = User::factory()->create(['role' => 'house_owner']);

        $building1 = Building::factory()->create(['owner_id' => $owner1->id]);
        $building2 = Building::factory()->create(['owner_id' => $owner2->id]);

        $flat1 = Flat::factory()->create(['building_id' => $building1->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);

        $owner1Flats = Flat::forOwner($owner1->id)->get();
        $owner2Flats = Flat::forOwner($owner2->id)->get();

        $this->assertCount(1, $owner1Flats);
        $this->assertTrue($owner1Flats->contains($flat1));
        $this->assertFalse($owner1Flats->contains($flat2));

        $this->assertCount(1, $owner2Flats);
        $this->assertTrue($owner2Flats->contains($flat2));
        $this->assertFalse($owner2Flats->contains($flat1));
    }

    /** @test */
    public function it_has_unique_flat_number_per_building()
    {
        $building = Building::factory()->create();

        Flat::factory()->create([
            'flat_number' => 'A101',
            'building_id' => $building->id
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Flat::factory()->create([
            'flat_number' => 'A101',
            'building_id' => $building->id
        ]);
    }

    /** @test */
    public function it_can_have_same_flat_number_in_different_buildings()
    {
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();

        $flat1 = Flat::factory()->create([
            'flat_number' => 'A101',
            'building_id' => $building1->id
        ]);

        $flat2 = Flat::factory()->create([
            'flat_number' => 'A101',
            'building_id' => $building2->id
        ]);

        $this->assertNotEquals($flat1->id, $flat2->id);
        $this->assertEquals('A101', $flat1->flat_number);
        $this->assertEquals('A101', $flat2->flat_number);
    }

    /** @test */
    public function it_can_cascade_delete_bills_when_flat_is_deleted()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = \App\Models\BillCategory::factory()->create(['building_id' => $building->id]);
        $bill = Bill::factory()->create([
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertDatabaseHas('bills', ['id' => $bill->id]);

        $flat->delete();

        $this->assertDatabaseMissing('bills', ['id' => $bill->id]);
    }
}



