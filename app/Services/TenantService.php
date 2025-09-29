<?php

namespace App\Services;

use App\Models\Tenant;
use App\Repositories\TenantRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TenantService
{
    public function __construct(private TenantRepositoryInterface $tenants)
    {
    }

    public function listForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator
    {
        return $this->tenants->paginateForUser($userId, $isAdmin, $perPage);
    }

    public function create(array $data): Tenant
    {
        return $this->tenants->create($data);
    }

    public function update(Tenant $tenant, array $data): bool
    {
        return $this->tenants->update($tenant, $data);
    }

    public function delete(Tenant $tenant): void
    {
        $this->tenants->delete($tenant);
    }
}
