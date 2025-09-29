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
        $admin = User::factory()->admin()->create();
        
        // Create test data
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();
        
        Flat::factory()->count(3)->create(['building_id' => $building1->id]);
        Flat::factory()->count(2)->create(['building_id' => $building2->id]);
        
        Tenant::factory()->count(2)->create(['building_id' => $building1->id]);
        Tenant::factory()->count(1)->create(['building_id' => $building2->id]);
        
        Bill::factory()->count(4)->create(['building_id' => $building1->id]);
        Bill::factory()->count(2)->create(['building_id' => $building2->id]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admin');
        $response->assertViewHas('totalFlats', 5);
        $response->assertViewHas('totalTenants', 3);
        $response->assertViewHas('totalBills', 6);
        $response->assertViewHas('buildings');
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



