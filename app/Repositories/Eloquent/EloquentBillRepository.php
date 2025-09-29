<?php

namespace App\Repositories\Eloquent;

use App\Models\Bill;
use App\Models\BillCategory;
use App\Models\Flat;
use App\Repositories\BillRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EloquentBillRepository implements BillRepositoryInterface
{
    public function getFlatsForUser(int $userId, bool $isAdmin): Collection
    {
        return $isAdmin
            ? Flat::with('building')->get()
            : Flat::forOwner($userId)->with('building')->get();
    }

    public function queryForUser(int $userId, bool $isAdmin): Builder
    {
        return $isAdmin ? Bill::query() : Bill::forOwner($userId);
    }

    public function paginateForUser(int $userId, bool $isAdmin, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->queryForUser($userId, $isAdmin)->with(['flat', 'billCategory', 'building']);

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }
        if (!empty($filters['month'])) {
            $query->forMonth($filters['month']);
        }
        if (!empty($filters['flat_id'])) {
            $query->forFlat((int)$filters['flat_id']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): Bill
    {
        return Bill::create($data);
    }

    public function update(Bill $bill, array $data): bool
    {
        return $bill->update($data);
    }

    public function delete(Bill $bill): ?bool
    {
        return $bill->delete();
    }
}
