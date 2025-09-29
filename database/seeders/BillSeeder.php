<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Flat;
use App\Models\BillCategory;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flats = Flat::all();
        $months = ['2024-01', '2024-02', '2024-03'];

        foreach ($flats as $flat) {
            $billCategories = $flat->building->billCategories;
            
            foreach ($months as $month) {
                foreach ($billCategories as $category) {
                    $amount = rand(50, 200);
                    $status = rand(0, 1) ? 'paid' : 'unpaid';
                    $dueAmount = $status === 'unpaid' ? rand(10, 50) : 0;
                    
                    Bill::create([
                        'month' => $month,
                        'amount' => $amount,
                        'due_amount' => $dueAmount,
                        'status' => $status,
                        'notes' => 'Monthly ' . strtolower($category->name) . ' bill',
                        'flat_id' => $flat->id,
                        'bill_category_id' => $category->id,
                        'building_id' => $flat->building_id,
                    ]);
                }
            }
        }
    }
}
