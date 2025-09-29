<?php

namespace App\Repositories;

use App\Models\BillCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BillCategoryRepositoryInterface
{
    public function queryForUser(int $userId, bool $isAdmin);

    public function paginateForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): BillCategory;

    public function update(BillCategory $category, array $data): bool;

    public function delete(BillCategory $category): ?bool;
}
