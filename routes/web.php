<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BillCategoryController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlatController;
use App\Http\Controllers\HouseOwnerController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth', 'multitenant'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // House Owners (admin only)
    Route::resource('house-owners', HouseOwnerController::class)
        ->middleware('admin')
        ->parameters([
            'house-owners' => 'house_owner'
        ]);

    // Buildings
    Route::get('buildings/{building_id}/flats', [BuildingController::class, 'flatsByBuilding'])
        ->name('buildings.flats');
    Route::resource('buildings', BuildingController::class)
        ->middleware('admin');

    // Flats
    Route::resource('flats', FlatController::class);

    // Tenants
    Route::resource('tenants', TenantController::class);

    // Bill Categories
    Route::resource('bill-categories', BillCategoryController::class);

    // Bills
    Route::resource('bills', BillController::class);
    Route::post('bills/{bill}/mark-paid', [BillController::class, 'markAsPaid'])
        ->name('bills.mark-paid');
});
