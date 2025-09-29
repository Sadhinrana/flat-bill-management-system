<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillCategoryRequest;
use App\Models\BillCategory;
use App\Models\Building;
use App\Services\BillCategoryService;
use Illuminate\Support\Facades\Auth;

class BillCategoryController extends Controller
{
    public function __construct(private BillCategoryService $billCategoryService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $billCategories = $this->billCategoryService->listForUser($user->id, $user->isAdmin(), 10);
        return view('bill-categories.index', compact('billCategories'));
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

        return view('bill-categories.create', compact('buildings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BillCategoryRequest $request)
    {
        $user = Auth::user();

        // Check if user can create bill categories for this building
        if ($user->isHouseOwner()) {
            $building = Building::find($request->building_id);
            if ($building->owner_id !== $user->id) {
                abort(403, 'Unauthorized access to building.');
            }
        }

        $this->billCategoryService->create($request->all());

        return redirect()->route('bill-categories.index')
            ->with('success', 'Bill category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BillCategory $billCategory)
    {
        $user = Auth::user();

        // Check if user can access this bill category
        if ($user->isHouseOwner() && $billCategory->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to bill category.');
        }

        $billCategory->load(['building', 'bills']);

        return view('bill-categories.show', compact('billCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BillCategory $billCategory)
    {
        $user = Auth::user();

        // Check if user can edit this bill category
        if ($user->isHouseOwner() && $billCategory->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to bill category.');
        }

        if ($user->isAdmin()) {
            $buildings = Building::all();
        } else {
            $buildings = Building::forOwner($user->id)->get();
        }

        return view('bill-categories.edit', compact('billCategory', 'buildings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BillCategoryRequest $request, BillCategory $billCategory)
    {
        $user = Auth::user();

        // Check if user can update this bill category
        if ($user->isHouseOwner() && $billCategory->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to bill category.');
        }

        // Check if user can assign bill category to this building
        if ($user->isHouseOwner()) {
            $building = Building::find($request->building_id);
            if ($building->owner_id !== $user->id) {
                abort(403, 'Unauthorized access to building.');
            }
        }

        $this->billCategoryService->update($billCategory, $request->all());

        return redirect()->route('bill-categories.index')
            ->with('success', 'Bill category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BillCategory $billCategory)
    {
        $user = Auth::user();

        // Check if user can delete this bill category
        if ($user->isHouseOwner() && $billCategory->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to bill category.');
        }

        $this->billCategoryService->delete($billCategory);

        return redirect()->route('bill-categories.index')
            ->with('success', 'Bill category deleted successfully.');
    }
}
