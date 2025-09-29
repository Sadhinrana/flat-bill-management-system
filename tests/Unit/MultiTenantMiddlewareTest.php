<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Http\Middleware\MultiTenantMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MultiTenantMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_admin_users_to_pass_through()
    {
        $admin = User::factory()->admin()->create();
        Auth::login($admin);

        $request = Request::create('/test', 'GET');
        $middleware = new MultiTenantMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
        $this->assertFalse($request->has('user_building_id'));
    }

    /** @test */
    public function it_adds_building_id_to_request_for_house_owner()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        Auth::login($houseOwner);

        $request = Request::create('/test', 'GET');
        $middleware = new MultiTenantMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
        $this->assertTrue($request->has('user_building_id'));
        $this->assertEquals($building->id, $request->get('user_building_id'));
    }

    /** @test */
    public function it_redirects_unauthenticated_users_to_login()
    {
        $request = Request::create('/test', 'GET');
        $middleware = new MultiTenantMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK');
        });

        $this->assertTrue($response->isRedirect());
        $this->assertEquals(route('login'), $response->getTargetUrl());
    }

    /** @test */
    public function it_handles_house_owner_without_building_id()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        Auth::login($houseOwner);

        $request = Request::create('/test', 'GET');
        $middleware = new MultiTenantMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
        $this->assertTrue($request->has('user_building_id'));
        $this->assertNull($request->get('user_building_id'));
    }

    /** @test */
    public function it_preserves_existing_request_data()
    {
        $houseOwner = User::factory()->houseOwner()->create();
        $building = Building::factory()->create(['owner_id' => $houseOwner->id]);
        Auth::login($houseOwner);

        $request = Request::create('/test', 'POST', ['existing_data' => 'test_value']);
        $middleware = new MultiTenantMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
        $this->assertEquals('test_value', $request->get('existing_data'));
        $this->assertEquals($building->id, $request->get('user_building_id'));
    }
}



