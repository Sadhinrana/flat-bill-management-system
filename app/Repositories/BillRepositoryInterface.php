<?php

namespace App\Repositories;

use App\Models\Bill;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BillRepositoryInterface
{
    public function getFlatsForUser(int $userId, bool $isAdmin): Collection;

    public function queryForUser(int $userId, bool $isAdmin);

    public function paginateForUser(int $userId, bool $isAdmin, array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Bill;

    public function update(Bill $bill, array $data): bool;

    public function delete(Bill $bill): ?bool;
}
