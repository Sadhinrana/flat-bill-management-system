<?php

namespace App\Repositories\Eloquent;

use App\Models\Tenant;
use App\Repositories\TenantRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentTenantRepository implements TenantRepositoryInterface
{
    public function queryForUser(int $userId, bool $isAdmin): Builder
    {
        return $isAdmin
            ? Tenant::query()->with(['building', 'flat'])
            : Tenant::forOwner($userId)->with(['building', 'flat']);
    }

    public function paginateForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator
    {
        return $this->queryForUser($userId, $isAdmin)->paginate($perPage);
    }

    public function create(array $data): Tenant
    {
        return Tenant::create($data);
    }

    public function update(Tenant $tenant, array $data): bool
    {
        return $tenant->update($data);
    }

    public function delete(Tenant $tenant): ?bool
    {
        return $tenant->delete();
    }
}
