<?php

namespace App\Repositories;

use App\Models\Building;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BuildingRepositoryInterface
{
    public function queryForUser(int $userId, bool $isAdmin);

    public function paginateForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Building;

    public function update(Building $building, array $data): bool;

    public function delete(Building $building): ?bool;
}
