<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuildingRequest;
use App\Models\Building;
use App\Models\Flat;
use App\Models\User;
use App\Services\BuildingService;
use Illuminate\Support\Facades\Auth;

class BuildingController extends Controller
{
    public function __construct(private BuildingService $buildingService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $buildings = $this->buildingService->listForUser($user->id, $user->isAdmin(), 10);
        return view('buildings.index', compact('buildings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $houseOwners = User::where('role', 'house_owner')
            ->whereDoesntHave('ownedBuilding')
            ->get();

        return view('buildings.create', compact('houseOwners'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BuildingRequest $request)
    {
        $this->buildingService->create($request->all());

        return redirect()->route('buildings.index')
            ->with('success', 'Building created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Building $building)
    {
        $user = Auth::user();

        $building->load(['flats', 'tenants', 'bills', 'billCategories']);

        return view('buildings.show', compact('building'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Building $building)
    {
        $houseOwners = User::where('role', 'house_owner')
            ->where(function($query) use ($building) {
                $query->whereDoesntHave('ownedBuilding')
                    ->orWhere('id', $building->owner_id);
            })
            ->get();

        return view('buildings.edit', compact('building', 'houseOwners'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BuildingRequest $request, Building $building)
    {
        $this->buildingService->update($building, $request->all());

        return redirect()->route('buildings.index')
            ->with('success', 'Building updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Building $building)
    {
        $this->buildingService->delete($building);

        return redirect()->route('buildings.index')
            ->with('success', 'Building deleted successfully.');
    }

    /**
     * Get all flats of a building.
     */
    public function flatsByBuilding($buildingId)
    {
        $flats = Flat::with('building')
            ->forBuilding($buildingId)
            ->get();

        return response()->json($flats);
    }
}
