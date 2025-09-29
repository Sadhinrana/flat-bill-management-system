<?php

namespace Tests\Unit;

use App\Models\Bill;
use App\Models\BillCategory;
use App\Models\Building;
use App\Models\Flat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BillTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_bill()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $bill = Bill::factory()->create([
            'month' => '2024-01',
            'amount' => 150.00,
            'due_amount' => 0.00,
            'status' => 'paid',
            'notes' => 'Monthly electricity bill',
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id,
            'building_id' => $building->id
        ]);

        $this->assertInstanceOf(Bill::class, $bill);
        $this->assertEquals('2024-01', $bill->month);
        $this->assertEquals(150.00, $bill->amount);
        $this->assertEquals('paid', $bill->status);
        $this->assertEquals($flat->id, $bill->flat_id);
    }

    #[Test]
    public function it_belongs_to_a_flat()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);
        $bill = Bill::factory()->create([
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertInstanceOf(Flat::class, $bill->flat);
        $this->assertEquals($flat->id, $bill->flat->id);
    }

    #[Test]
    public function it_belongs_to_a_bill_category()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);
        $bill = Bill::factory()->create([
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertInstanceOf(BillCategory::class, $bill->billCategory);
        $this->assertEquals($category->id, $bill->billCategory->id);
    }

    #[Test]
    public function it_belongs_to_a_building()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);
        $bill = Bill::factory()->create([
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertInstanceOf(Building::class, $bill->building);
        $this->assertEquals($building->id, $bill->building->id);
    }

    #[Test]
    public function it_can_scope_bills_for_building()
    {
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();

        $flat1 = Flat::factory()->create(['building_id' => $building1->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);

        $category1 = BillCategory::factory()->create(['building_id' => $building1->id]);
        $category2 = BillCategory::factory()->create(['building_id' => $building2->id]);

        $bill1 = Bill::factory()->create([
            'flat_id' => $flat1->id,
            'building_id' => $building1->id,
            'bill_category_id' => $category1->id
        ]);
        $bill2 = Bill::factory()->create([
            'flat_id' => $flat2->id,
            'building_id' => $building2->id,
            'bill_category_id' => $category2->id
        ]);

        $building1Bills = Bill::forBuilding($building1->id)->get();
        $building2Bills = Bill::forBuilding($building2->id)->get();

        $this->assertCount(1, $building1Bills);
        $this->assertTrue($building1Bills->contains($bill1));
        $this->assertFalse($building1Bills->contains($bill2));

        $this->assertCount(1, $building2Bills);
        $this->assertTrue($building2Bills->contains($bill2));
        $this->assertFalse($building2Bills->contains($bill1));
    }

    #[Test]
    public function it_can_scope_bills_for_owner()
    {
        $owner1 = User::factory()->create(['role' => 'house_owner']);
        $owner2 = User::factory()->create(['role' => 'house_owner']);

        $building1 = Building::factory()->create(['owner_id' => $owner1->id]);
        $building2 = Building::factory()->create(['owner_id' => $owner2->id]);

        $flat1 = Flat::factory()->create(['building_id' => $building1->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building2->id]);

        $category1 = BillCategory::factory()->create(['building_id' => $building1->id]);
        $category2 = BillCategory::factory()->create(['building_id' => $building2->id]);

        $bill1 = Bill::factory()->create([
            'flat_id' => $flat1->id,
            'building_id' => $building1->id,
            'bill_category_id' => $category1->id
        ]);
        $bill2 = Bill::factory()->create([
            'flat_id' => $flat2->id,
            'building_id' => $building2->id,
            'bill_category_id' => $category2->id
        ]);

        $owner1Bills = Bill::forOwner($owner1->id)->get();
        $owner2Bills = Bill::forOwner($owner2->id)->get();

        $this->assertCount(1, $owner1Bills);
        $this->assertTrue($owner1Bills->contains($bill1));
        $this->assertFalse($owner1Bills->contains($bill2));

        $this->assertCount(1, $owner2Bills);
        $this->assertTrue($owner2Bills->contains($bill2));
        $this->assertFalse($owner2Bills->contains($bill1));
    }

    #[Test]
    public function it_can_scope_bills_for_flat()
    {
        $building = Building::factory()->create();
        $flat1 = Flat::factory()->create(['building_id' => $building->id]);
        $flat2 = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $bill1 = Bill::factory()->create([
            'flat_id' => $flat1->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);
        $bill2 = Bill::factory()->create([
            'flat_id' => $flat2->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $flat1Bills = Bill::forFlat($flat1->id)->get();
        $flat2Bills = Bill::forFlat($flat2->id)->get();

        $this->assertCount(1, $flat1Bills);
        $this->assertTrue($flat1Bills->contains($bill1));
        $this->assertFalse($flat1Bills->contains($bill2));

        $this->assertCount(1, $flat2Bills);
        $this->assertTrue($flat2Bills->contains($bill2));
        $this->assertFalse($flat2Bills->contains($bill1));
    }

    #[Test]
    public function it_can_scope_bills_by_status()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $paidBill = Bill::factory()->create([
            'status' => 'paid',
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);
        $unpaidBill = Bill::factory()->create([
            'status' => 'unpaid',
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $paidBills = Bill::byStatus('paid')->get();
        $unpaidBills = Bill::byStatus('unpaid')->get();

        $this->assertCount(1, $paidBills);
        $this->assertTrue($paidBills->contains($paidBill));
        $this->assertFalse($paidBills->contains($unpaidBill));

        $this->assertCount(1, $unpaidBills);
        $this->assertTrue($unpaidBills->contains($unpaidBill));
        $this->assertFalse($unpaidBills->contains($paidBill));
    }

    #[Test]
    public function it_can_scope_bills_for_month()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $janBill = Bill::factory()->create([
            'month' => '2024-01',
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);
        $febBill = Bill::factory()->create([
            'month' => '2024-02',
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $janBills = Bill::forMonth('2024-01')->get();
        $febBills = Bill::forMonth('2024-02')->get();

        $this->assertCount(1, $janBills);
        $this->assertTrue($janBills->contains($janBill));
        $this->assertFalse($janBills->contains($febBill));

        $this->assertCount(1, $febBills);
        $this->assertTrue($febBills->contains($febBill));
        $this->assertFalse($febBills->contains($janBill));
    }

    #[Test]
    public function it_can_check_if_bill_is_paid()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $paidBill = Bill::factory()->create([
            'status' => 'paid',
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);
        $unpaidBill = Bill::factory()->create([
            'status' => 'unpaid',
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertTrue($paidBill->isPaid());
        $this->assertFalse($unpaidBill->isPaid());
    }

    #[Test]
    public function it_can_check_if_bill_is_unpaid()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $paidBill = Bill::factory()->create([
            'status' => 'paid',
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);
        $unpaidBill = Bill::factory()->create([
            'status' => 'unpaid',
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertFalse($paidBill->isUnpaid());
        $this->assertTrue($unpaidBill->isUnpaid());
    }

    #[Test]
    public function it_can_mark_bill_as_paid()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $bill = Bill::factory()->create([
            'status' => 'unpaid',
            'amount' => 100.00,
            'due_amount' => 100.00,
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertEquals('unpaid', $bill->status);
        $this->assertEquals(100.00, $bill->due_amount);

        $bill->markAsPaid();

        $this->assertEquals('paid', $bill->fresh()->status);
        $this->assertEquals(0.00, $bill->fresh()->due_amount);
    }

    #[Test]
    public function it_can_mark_bill_as_unpaid()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $bill = Bill::factory()->create([
            'status' => 'paid',
            'amount' => 100.00,
            'due_amount' => 0.00,
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertEquals('paid', $bill->status);
        $this->assertEquals(0.00, $bill->due_amount);

        $bill->markAsUnpaid();

        $this->assertEquals('unpaid', $bill->fresh()->status);
        $this->assertEquals(100.00, $bill->fresh()->due_amount);
    }

    #[Test]
    public function it_casts_amount_fields_to_decimal()
    {
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $bill = Bill::factory()->create([
            'amount' => '150.50',
            'due_amount' => '75.25',
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $this->assertIsFloat($bill->amount);
        $this->assertIsFloat($bill->due_amount);
        $this->assertEquals(150.50, $bill->amount);
        $this->assertEquals(75.25, $bill->due_amount);
    }
}



