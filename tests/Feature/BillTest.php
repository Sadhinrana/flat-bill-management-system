<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Tenant;
use App\Models\BillCategory;
use App\Models\Bill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class BillTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function admin_can_view_bills_index()
    {
        $admin = User::factory()->admin()->create();
        $bills = Bill::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/bills');

        $response->assertStatus(200);
        $response->assertViewIs('bills.index');
        $response->assertViewHas('bills');
    }

    /** @test */
    public function house_owner_can_view_their_building_bills()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);

        $bills = Bill::factory()->count(3)->create(['building_id' => $building->id]);
        Bill::factory()->count(2)->create(); // Other building's bills

        $response = $this->actingAs($houseOwner)->get('/bills');

        $response->assertStatus(200);
        $response->assertViewIs('bills.index');
        $response->assertViewHas('bills');
    }

    /** @test */
    public function admin_can_create_bill()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $billData = [
            'month' => '2024-01',
            'amount' => 150.00,
            'due_amount' => 150.00,
            'status' => 'unpaid',
            'notes' => 'Monthly electricity bill',
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id,
            'building_id' => $building->id,
        ];

        $response = $this->actingAs($admin)->post('/bills', $billData);

        $response->assertRedirect('/bills');
        $this->assertDatabaseHas('bills', $billData);

        // Check if email was sent
        Mail::assertSent(\App\Mail\BillCreated::class);
    }

    /** @test */
    public function house_owner_can_create_bill_in_their_building()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $billData = [
            'month' => '2024-01',
            'amount' => 150.00,
            'due_amount' => 150.00,
            'status' => 'unpaid',
            'notes' => 'Monthly electricity bill',
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id,
            'building_id' => $building->id,
        ];

        $response = $this->actingAs($houseOwner)->post('/bills', $billData);

        $response->assertRedirect('/bills');
        $this->assertDatabaseHas('bills', $billData);

        // Check if email was sent
        Mail::assertSent(\App\Mail\BillCreated::class);
    }

    /** @test */
    public function house_owner_cannot_create_bill_in_other_building()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);

        $otherBuilding = Building::factory()->create();
        $otherFlat = Flat::factory()->create(['building_id' => $otherBuilding->id]);
        $otherCategory = BillCategory::factory()->create(['building_id' => $otherBuilding->id]);

        $billData = [
            'month' => '2024-01',
            'amount' => 150.00,
            'due_amount' => 150.00,
            'status' => 'unpaid',
            'notes' => 'Monthly electricity bill',
            'flat_id' => $otherFlat->id,
            'bill_category_id' => $otherCategory->id,
            'building_id' => $otherBuilding->id,
        ];

        $response = $this->actingAs($houseOwner)->post('/bills', $billData);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_bill_details()
    {
        $admin = User::factory()->admin()->create();
        $bill = Bill::factory()->create();

        $response = $this->actingAs($admin)->get("/bills/{$bill->id}");

        $response->assertStatus(200);
        $response->assertViewIs('bills.show');
        $response->assertViewHas('bill', $bill);
    }

    /** @test */
    public function admin_can_update_bill()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        // Create bill with specific relationships
        $bill = Bill::factory()->create([
            'building_id' => $building->id,
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id
        ]);

        $updateData = [
            'month' => '2024-02',
            'amount' => 200.00,
            'due_amount' => 200.00,
            'status' => 'unpaid',
            'notes' => 'Updated bill notes',
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id,
            'building_id' => $building->id
        ];

        $response = $this->actingAs($admin)->put("/bills/{$bill->id}", $updateData);

        $response->assertRedirect("/bills");
        $this->assertDatabaseHas('bills', array_merge(['id' => $bill->id], $updateData));
    }

    /** @test */
    public function admin_can_delete_bill()
    {
        $admin = User::factory()->admin()->create();
        $bill = Bill::factory()->create();

        $response = $this->actingAs($admin)->delete("/bills/{$bill->id}");

        $response->assertRedirect('/bills');
        $this->assertDatabaseMissing('bills', ['id' => $bill->id]);
    }

    /** @test */
    public function admin_can_mark_bill_as_paid()
    {
        $admin = User::factory()->admin()->create();
        $bill = Bill::factory()->unpaid()->create();

        $this->actingAs($admin)->post("/bills/{$bill->id}/mark-paid");

        $this->assertDatabaseHas('bills', [
            'id' => $bill->id,
            'status' => 'paid',
            'due_amount' => 0.00
        ]);

        // Check if email was sent
        Mail::assertSent(\App\Mail\BillPaid::class);
    }

    /** @test */
    public function house_owner_can_mark_bill_as_paid_in_their_building()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);
        $bill = Bill::factory()->unpaid()->create(['building_id' => $building->id]);

        $this->actingAs($houseOwner)->post("/bills/{$bill->id}/mark-paid");

        $this->assertDatabaseHas('bills', [
            'id' => $bill->id,
            'status' => 'paid',
            'due_amount' => 0.00
        ]);

        // Check if email was sent
        Mail::assertSent(\App\Mail\BillPaid::class);
    }

    /** @test */
    public function house_owner_cannot_mark_other_building_bill_as_paid()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        $houseOwner->update(['building_id' => $building->id]);

        $otherBill = Bill::factory()->unpaid()->create();

        $response = $this->actingAs($houseOwner)->post("/bills/{$otherBill->id}/mark-paid");

        $response->assertStatus(403);
    }

    /** @test */
    public function bill_creation_requires_valid_data()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/bills', []);

        $response->assertSessionHasErrors(['month', 'amount', 'flat_id', 'bill_category_id']);
    }

    /** @test */
    public function bill_amount_must_be_numeric()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $billData = [
            'month' => '2024-01',
            'amount' => 'invalid',
            'due_amount' => 'invalid',
            'status' => 'unpaid',
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id,
            'building_id' => $building->id,
        ];

        $response = $this->actingAs($admin)->post('/bills', $billData);

        $response->assertSessionHasErrors(['amount', 'due_amount']);
    }

    /** @test */
    public function bill_status_must_be_valid()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        $billData = [
            'month' => '2024-01',
            'amount' => 150.00,
            'due_amount' => 150.00,
            'status' => 'invalid_status',
            'flat_id' => $flat->id,
            'bill_category_id' => $category->id,
            'building_id' => $building->id,
        ];

        $response = $this->actingAs($admin)->post('/bills', $billData);

        $response->assertSessionHasErrors(['status']);
    }

    /** @test */
    public function bill_can_be_filtered_by_status()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        Bill::factory()->paid()->create([
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);
        Bill::factory()->unpaid()->create([
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $response = $this->actingAs($admin)->get('/bills?status=paid');

        $response->assertStatus(200);
        $response->assertViewHas('bills');
    }

    /** @test */
    public function bill_can_be_filtered_by_month()
    {
        $admin = User::factory()->admin()->create();
        $building = Building::factory()->create();
        $flat = Flat::factory()->create(['building_id' => $building->id]);
        $category = BillCategory::factory()->create(['building_id' => $building->id]);

        Bill::factory()->create([
            'month' => '2024-01',
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);
        Bill::factory()->create([
            'month' => '2024-02',
            'flat_id' => $flat->id,
            'building_id' => $building->id,
            'bill_category_id' => $category->id
        ]);

        $response = $this->actingAs($admin)->get('/bills?month=2024-01');

        $response->assertStatus(200);
        $response->assertViewHas('bills');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_bills()
    {
        $response = $this->get('/bills');

        $response->assertRedirect('/login');
    }
}



