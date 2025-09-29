<?php

namespace App\Services;

use App\Models\BillCategory;
use App\Repositories\BillCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BillCategoryService
{
    public function __construct(private BillCategoryRepositoryInterface $categories)
    {
    }

    public function listForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator
    {
        return $this->categories->paginateForUser($userId, $isAdmin, $perPage);
    }

    public function create(array $data): BillCategory
    {
        return $this->categories->create($data);
    }

    public function update(BillCategory $category, array $data): bool
    {
        return $this->categories->update($category, $data);
    }

    public function delete(BillCategory $category): void
    {
        $this->categories->delete($category);
    }
}
