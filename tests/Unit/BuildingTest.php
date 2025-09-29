<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Tenant;
use App\Models\BillCategory;
use App\Models\Bill;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuildingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_building()
    {
        $user = User::factory()->create(['role' => 'house_owner']);
        $building = Building::factory()->create([
            'name' => 'Sunset Apartments',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'owner_id' => $user->id
        ]);

        $this->assertInstanceOf(Building::class, $building);
        $this->assertEquals('Sunset Apartments', $building->name);
        $this->assertEquals('123 Main St', $building->address);
        $this->assertEquals($user->id, $building->owner_id);
    }

    #[Test]
    public function it_belongs_to_an_owner()
    {
        $user = User::factory()->create(['role' => 'house_owner']);
        $building = Building::factory()->create(['owner_id' => $user->id]);

        $this->assertInstanceOf(User::class, $building->owner);
        $this->assertEquals($user->id, $building->owner->id);
    }

    #[Test]
    public function it_can_have_multiple_flats()
    {
        $building = Building::factory()->create();
        $flat1 = Flat::factory()->create(['building_id' => $building->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building->id]);

        $this->assertCount(2, $building->flats);
        $this->assertTrue($building->flats->contains($flat1));
        $this->assertTrue($building->flats->contains($flat2));
    }

    #[Test]
    public function it_can_have_multiple_tenants()
    {
        $building = Building::factory()->create();
        $tenant1 = Tenant::factory()->create(['building_id' => $building->id]);
        $tenant2 = Tenant::factory()->create(['building_id' => $building->id]);

        $this->assertCount(2, $building->tenants);
        $this->assertTrue($building->tenants->contains($tenant1));
        $this->assertTrue($building->tenants->contains($tenant2));
    }

    #[Test]
    public function it_can_have_multiple_bill_categories()
    {
        $building = Building::factory()->create();
        $category1 = BillCategory::factory()->create(['building_id' => $building->id]);
        $category2 = BillCategory::factory()->create(['building_id' => $building->id]);

        $this->assertCount(2, $building->billCategories);
        $this->assertTrue($building->billCategories->contains($category1));
        $this->assertTrue($building->billCategories->contains($category2));
    }

    #[Test]
    public function it_can_have_multiple_bills()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $bill1 = Bill::factory()->create([
            'building_id' => $building->id,
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id
        ]);
        $bill2 = Bill::factory()->create([
            'building_id' => $building->id,
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertCount(2, $building->bills);
        $this->assertTrue($building->bills->contains($bill1));
        $this->assertTrue($building->bills->contains($bill2));
    }

    #[Test]
    public function it_can_scope_buildings_for_owner()
    {
        $owner1 = User::factory()->create(['role' => 'house_owner']);
        $owner2 = User::factory()->create(['role' => 'house_owner']);

        $building1 = Building::factory()->create(['owner_id' => $owner1->id]);
        $building2 = Building::factory()->create(['owner_id' => $owner2->id]);

        $owner1Buildings = Building::forOwner($owner1->id)->get();
        $owner2Buildings = Building::forOwner($owner2->id)->get();

        $this->assertCount(1, $owner1Buildings);
        $this->assertTrue($owner1Buildings->contains($building1));
        $this->assertFalse($owner1Buildings->contains($building2));

        $this->assertCount(1, $owner2Buildings);
        $this->assertTrue($owner2Buildings->contains($building2));
        $this->assertFalse($owner2Buildings->contains($building1));
    }

    #[Test]
    public function it_can_cascade_delete_flats_when_building_is_deleted()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);

        $this->assertDatabaseHas('flats', ['id' => $flat->id]);

        $building->delete();

        $this->assertDatabaseMissing('flats', ['id' => $flat->id]);
    }

    #[Test]
    public function it_can_cascade_delete_tenants_when_building_is_deleted()
    {
        $building = Building::factory()->create();
        $tenant = Tenant::factory()->create(['building_id' => $building->id]);

        $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);

        $building->delete();

        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }

    #[Test]
    public function it_can_cascade_delete_bill_categories_when_building_is_deleted()
    {
        $building = Building::factory()->create();
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $this->assertDatabaseHas('bill_categories', ['id' => $category->id]);

        $building->delete();

        $this->assertDatabaseMissing('bill_categories', ['id' => $category->id]);
    }

    #[Test]
    public function it_can_cascade_delete_bills_when_building_is_deleted()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);
        $bill = Bill::factory()->create([
            'building_id' => $building->id,
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertDatabaseHas('bills', ['id' => $bill->id]);

        $building->delete();

        $this->assertDatabaseMissing('bills', ['id' => $bill->id]);
    }
}



