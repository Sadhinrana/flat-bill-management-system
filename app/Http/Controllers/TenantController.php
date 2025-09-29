<?php

namespace App\Http\Controllers;

use App\Http\Requests\TenantRequest;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function __construct(private TenantService $tenantService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $tenants = $this->tenantService->listForUser($user->id, $user->isAdmin(), 10);
        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $buildings = Building::all();
        } else {
            $buildings = Building::forOwner($user->id)->get();
        }

        return view('tenants.create', compact('buildings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TenantRequest $request)
    {
        $user = Auth::user();

        // Check if user can create tenants for this building
        if ($user->isHouseOwner()) {
            $building = Building::find($request->building_id);
            if ($building->owner_id !== $user->id) {
                abort(403, 'Unauthorized access to building.');
            }
        }

        $this->tenantService->create($request->all());

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        $user = Auth::user();

        // Check if user can access this tenant
        if ($user->isHouseOwner() && $tenant->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to tenant.');
        }

        $tenant->load(['building', 'flat']);

        return view('tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant)
    {
        $user = Auth::user();

        // Check if user can edit this tenant
        if ($user->isHouseOwner() && $tenant->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to tenant.');
        }

        if ($user->isAdmin()) {
            $buildings = Building::all();
            $flats = Flat::with('building')
                ->forBuilding($tenant->building_id)
                ->get();
        } else {
            $buildings = Building::forOwner($user->id)->get();
            $flats = Flat::forOwner($user->id)
                ->forBuilding($tenant->building_id)
                ->with('building')
                ->get();
        }

        return view('tenants.edit', compact('tenant', 'buildings', 'flats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TenantRequest $request, Tenant $tenant)
    {
        $user = Auth::user();

        // Check if user can update this tenant
        if ($user->isHouseOwner() && $tenant->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to tenant.');
        }

        // Check if user can assign tenant to this building
        if ($user->isHouseOwner()) {
            $building = Building::find($request->building_id);
            if ($building->owner_id !== $user->id) {
                abort(403, 'Unauthorized access to building.');
            }
        }

        $this->tenantService->update($tenant, $request->all());

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        $user = Auth::user();

        // Check if user can delete this tenant
        if ($user->isHouseOwner() && $tenant->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to tenant.');
        }

        $this->tenantService->delete($tenant);

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }
}
