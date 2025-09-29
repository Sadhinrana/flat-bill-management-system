<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillRequest;
use App\Models\Bill;
use App\Models\BillCategory;
use App\Models\Flat;
use App\Services\BillService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    public function __construct(private BillService $billService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        [$bills, $flats] = $this->billService->listForUser(
            $user->id,
            $user->isAdmin(),
            $request->only(['status', 'month', 'flat_id']),
            10
        );

        return view('bills.index', compact('bills', 'flats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $flats = Flat::with('building')->get();
            $billCategories = BillCategory::all();
        } else {
            $flats = Flat::forOwner($user->id)->with('building')->get();
            $billCategories = BillCategory::forOwner($user->id)->get();
        }

        return view('bills.create', compact('flats', 'billCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BillRequest $request)
    {
        $user = Auth::user();

        $flat = Flat::find($request->flat_id);

        // Authorization check for owners creating in their building
        if ($user->isHouseOwner() && $flat->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to flat.');
        }

        $this->billService->createBill($request->all());

        return redirect()->route('bills.index')
            ->with('success', 'Bill created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bill $bill)
    {
        $user = Auth::user();

        // Check if user can access this bill
        if ($user->isHouseOwner() && $bill->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to bill.');
        }

        $bill->load(['flat', 'billCategory', 'building']);

        return view('bills.show', compact('bill'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bill $bill)
    {
        $user = Auth::user();

        // Check if user can edit this bill
        if ($user->isHouseOwner() && $bill->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to bill.');
        }

        if ($user->isAdmin()) {
            $flats = Flat::with('building')->get();
            $billCategories = BillCategory::all();
        } else {
            $flats = Flat::forOwner($user->id)->with('building')->get();
            $billCategories = BillCategory::forOwner($user->id)->get();
        }

        return view('bills.edit', compact('bill', 'flats', 'billCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BillRequest $request, Bill $bill)
    {
        $user = Auth::user();

        // Check if user can update this bill
        if ($user->isHouseOwner() && $bill->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to bill.');
        }

        $flat = Flat::find($request->flat_id);

        // Check if user can assign bill to this flat
        if ($user->isHouseOwner() && $flat->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to flat.');
        }

        $this->billService->updateBill($bill, $request->all());

        return redirect()->route('bills.index')
            ->with('success', 'Bill updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bill $bill)
    {
        $user = Auth::user();

        // Check if user can delete this bill
        if ($user->isHouseOwner() && $bill->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to bill.');
        }

        $this->billService->delete($bill);

        return redirect()->route('bills.index')
            ->with('success', 'Bill deleted successfully.');
    }

    /**
     * Mark bill as paid
     */
    public function markAsPaid(Bill $bill)
    {
        $user = Auth::user();

        // Check if user can update this bill
        if ($user->isHouseOwner() && $bill->building->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to bill.');
        }

        $this->billService->markAsPaid($bill);

        return redirect()->back()
            ->with('success', 'Bill marked as paid successfully.');
    }
}
