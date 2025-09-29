<?php

namespace App\Services;

use App\Mail\BillCreated;
use App\Mail\BillPaid;
use App\Models\Bill;
use App\Models\Flat;
use App\Repositories\BillRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;

class BillService
{
    public function __construct(private BillRepositoryInterface $bills)
    {
    }

    public function listForUser(int $userId, bool $isAdmin, array $filters = [], int $perPage = 10): array
    {
        $flats = $this->bills->getFlatsForUser($userId, $isAdmin);
        $paginated = $this->bills->paginateForUser($userId, $isAdmin, $filters, $perPage);
        return [$paginated, $flats];
    }

    public function createBill(array $data): Bill
    {
        $flat = Flat::findOrFail($data['flat_id']);
        $data['building_id'] = $flat->building_id;
        $data['status'] = $data['status'] ?? 'unpaid';

        $bill = $this->bills->create($data);

        try {
            Mail::to($flat->owner_email)->send(new BillCreated($bill));
        } catch (\Exception $e) {
            \Log::error('Failed to send bill created email: ' . $e->getMessage());
        }

        return $bill;
    }

    public function updateBill(Bill $bill, array $data): Bill
    {
        $wasPaid = $bill->isPaid();
        $flat = Flat::findOrFail($data['flat_id']);
        $data['building_id'] = $flat->building_id;

        $this->bills->update($bill, $data);

        if (!$wasPaid && $bill->isPaid()) {
            try {
                Mail::to($flat->owner_email)->send(new BillPaid($bill));
            } catch (\Exception $e) {
                \Log::error('Failed to send bill paid email: ' . $e->getMessage());
            }
        }

        return $bill;
    }

    public function markAsPaid(Bill $bill): Bill
    {
        $bill->markAsPaid();
        try {
            Mail::to($bill->flat->owner_email)->send(new BillPaid($bill));
        } catch (\Exception $e) {
            \Log::error('Failed to send bill paid email: ' . $e->getMessage());
        }
        return $bill;
    }

    public function delete(Bill $bill): void
    {
        $this->bills->delete($bill);
    }
}
