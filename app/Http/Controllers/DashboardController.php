<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin dashboard - show all data
            $buildings = Building::with('owner')->get();
            $totalFlats = Flat::count();
            $totalTenants = Tenant::count();
            $totalBills = Bill::count();
            $unpaidBills = Bill::where('status', 'unpaid')->count();
            $paidBills = Bill::where('status', 'paid')->count();

            return view('dashboard.admin', compact(
                'buildings',
                'totalFlats',
                'totalTenants',
                'totalBills',
                'unpaidBills',
                'paidBills'
            ));
        } else {
            // House owner dashboard - show only their data
            $building = $user->ownedBuilding;
            $flats = $building ? $building->flats : collect();
            $tenants = $building ? $building->tenants : collect();
            $bills = $building ? $building->bills : collect();
            $billCategories = $building ? $building->billCategories : collect();

            $totalFlats = $flats->count();
            $totalTenants = $tenants->count();
            $totalBills = $bills->count();
            $unpaidBills = $bills->where('status', 'unpaid')->count();
            $paidBills = $bills->where('status', 'paid')->count();

            return view('dashboard.house_owner', compact(
                'building',
                'flats',
                'tenants',
                'bills',
                'billCategories',
                'totalFlats',
                'totalTenants',
                'totalBills',
                'unpaidBills',
                'paidBills'
            ));
        }
    }
}
