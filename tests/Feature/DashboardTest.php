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

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_dashboard_with_all_data()
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->admin()->create();

        // Create test data with specific counts
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();

        // Create exactly 3 flats for building1
        Flat::factory(3)->create([
            'building_id' => $building1->id
        ]);

        // Create exactly 2 flats for building2
        Flat::factory(2)->create([
            'building_id' => $building2->id
        ]);

        // Create tenants
        Tenant::factory(2)->create([
            'building_id' => $building1->id,
            'flat_id' => Flat::where('building_id', $building1->id)->first()->id
        ]);
        Tenant::factory()->create([
            'building_id' => $building2->id,
            'flat_id' => Flat::where('building_id', $building2->id)->first()->id
        ]);

        // Create bills
        $category1 = BillCategory::factory()->create(['building_id' => $building1->id]);
        $category2 = BillCategory::factory()->create(['building_id' => $building2->id]);

        Bill::factory(4)->create([
            'building_id' => $building1->id,
            'flat_id' => Flat::where('building_id', $building1->id)->first()->id,
            'bill_category_id' => $category1->id
        ]);
        Bill::factory(2)->create([
            'building_id' => $building2->id,
            'flat_id' => Flat::where('building_id', $building2->id)->first()->id,
            'bill_category_id' => $category2->id
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200)
            ->assertViewIs('dashboard.admin')
            ->assertViewHas('totalFlats', 5)
            ->assertViewHas('totalTenants', 3)
            ->assertViewHas('totalBills', 6)
            ->assertViewHas('buildings');
    }

    /** @test */
    public function house_owner_can_view_dashboard_with_their_building_data()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);

        // Update user's building_id
        $houseOwner->update(['building_id' => $building->id]);

        // Create test data for this building
        Flat::factory()->count(3)->create(['building_id' => $building->id]);
        Tenant::factory()->count(2)->create(['building_id' => $building->id]);
        Bill::factory()->count(4)->create(['building_id' => $building->id]);

        $response = $this->actingAs($houseOwner)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.house_owner');
        $response->assertViewHas('totalFlats', 3);
        $response->assertViewHas('totalTenants', 2);
        $response->assertViewHas('totalBills', 4);
        $response->assertViewHas('building', $building);
    }

    /** @test */
    public function house_owner_without_building_sees_no_building_message()
    {
        $houseOwner = User::factory()->houseOwner()->create();

        $response = $this->actingAs($houseOwner)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.house_owner');
        $response->assertViewHas('building', null);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}



