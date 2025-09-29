<?php

namespace App\Repositories\Eloquent;

use App\Models\BillCategory;
use App\Repositories\BillCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentBillCategoryRepository implements BillCategoryRepositoryInterface
{
    public function queryForUser(int $userId, bool $isAdmin): Builder
    {
        return $isAdmin
            ? BillCategory::query()->with(['building', 'bills'])
            : BillCategory::forOwner($userId)->with(['building', 'bills']);
    }

    public function paginateForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator
    {
        return $this->queryForUser($userId, $isAdmin)->paginate($perPage);
    }

    public function create(array $data): BillCategory
    {
        return BillCategory::create($data);
    }

    public function update(BillCategory $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(BillCategory $category): ?bool
    {
        return $category->delete();
    }
}
