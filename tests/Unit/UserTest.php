<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'admin'
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('admin', $user->role);
    }

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $houseOwner = User::factory()->create(['role' => 'house_owner']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($houseOwner->isAdmin());
    }

    /** @test */
    public function it_can_check_if_user_is_house_owner()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $houseOwner = User::factory()->create(['role' => 'house_owner']);

        $this->assertFalse($admin->isHouseOwner());
        $this->assertTrue($houseOwner->isHouseOwner());
    }

    /** @test */
    public function it_can_have_a_building()
    {
        $user = User::factory()->create(['role' => 'house_owner']);
        $building = Building::factory()->create(['owner_id' => $user->id]);

        $this->assertInstanceOf(Building::class, $user->ownedBuilding);
        $this->assertEquals($building->id, $user->ownedBuilding->id);
    }

    /** @test */
    public function it_can_scope_users_by_role()
    {
        User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'house_owner']);
        User::factory()->create(['role' => 'house_owner']);

        $admins = User::where('role', 'admin')->get();
        $houseOwners = User::where('role', 'house_owner')->get();

        $this->assertCount(1, $admins);
        $this->assertCount(2, $houseOwners);
    }

    /** @test */
    public function it_hashes_password_when_creating_user()
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(password_verify('password123', $user->password));
    }

    /** @test */
    public function it_can_update_user_password()
    {
        $user = User::factory()->create(['password' => 'oldpassword']);
        $oldPasswordHash = $user->password;

        $user->update(['password' => 'newpassword']);

        $this->assertNotEquals($oldPasswordHash, $user->password);
        $this->assertTrue(password_verify('newpassword', $user->password));
    }
}



