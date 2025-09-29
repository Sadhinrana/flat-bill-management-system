<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlatRequest;
use App\Models\Building;
use App\Models\Flat;
use App\Services\FlatService;
use Illuminate\Support\Facades\Auth;

class FlatController extends Controller
{
    public function __construct(private FlatService $flatService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $flats = $this->flatService->listForUser($user->id, $user->isAdmin(), 10);
        return view('flats.index', compact('flats'));
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

        return view('flats.create', compact('buildings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FlatRequest $request)
    {
        $user = Auth::user();

        // Check if user can create flats in this building
        if ($user->isHouseOwner()) {
            $building = Building::find($request->building_id);
            if ($building->owner_id !== $user->id) {
                abort(403, 'Unauthorized access to building.');
            }
        }

        $this->flatService->create($request->all());

        return redirect()->route('flats.index')
            ->with('success', 'Flat created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Flat $flat)
    {
        $user = Auth::user();

        // Check if user can access this flat
        if ($user->isHouseOwner() && $flat->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to flat.');
        }

        $flat->load(['building', 'tenants', 'bills']);

        return view('flats.show', compact('flat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Flat $flat)
    {
        $user = Auth::user();

        // Check if user can edit this flat
        if ($user->isHouseOwner() && $flat->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to flat.');
        }

        if ($user->isAdmin()) {
            $buildings = Building::all();
        } else {
            $buildings = Building::forOwner($user->id)->get();
        }

        return view('flats.edit', compact('flat', 'buildings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FlatRequest $request, Flat $flat)
    {
        $user = Auth::user();

        // Check if user can update this flat
        if ($user->isHouseOwner() && $flat->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to flat.');
        }

        // Check if user can assign flat to this building
        if ($user->isHouseOwner()) {
            $building = Building::find($request->building_id);
            if ($building->owner_id !== $user->id) {
                abort(403, 'Unauthorized access to building.');
            }
        }

        $this->flatService->update($flat, $request->all());

        return redirect()->route('flats.index')
            ->with('success', 'Flat updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Flat $flat)
    {
        $user = Auth::user();

        // Check if user can delete this flat
        if ($user->isHouseOwner() && $flat->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to flat.');
        }

        $this->flatService->delete($flat);

        return redirect()->route('flats.index')
            ->with('success', 'Flat deleted successfully.');
    }
}
