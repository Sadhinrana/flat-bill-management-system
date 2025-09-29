<?php

namespace App\Repositories;

use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TenantRepositoryInterface
{
    public function queryForUser(int $userId, bool $isAdmin);

    public function paginateForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Tenant;

    public function update(Tenant $tenant, array $data): bool;

    public function delete(Tenant $tenant): ?bool;
}
