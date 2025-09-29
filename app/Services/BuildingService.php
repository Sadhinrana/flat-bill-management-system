<?php

namespace App\Services;

use App\Models\Building;
use App\Repositories\BuildingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BuildingService
{
    public function __construct(private BuildingRepositoryInterface $buildings)
    {
    }

    public function listForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator
    {
        return $this->buildings->paginateForUser($userId, $isAdmin, $perPage);
    }

    public function create(array $data): Building
    {
        return $this->buildings->create($data);
    }

    public function update(Building $building, array $data): bool
    {
        return $this->buildings->update($building, $data);
    }

    public function delete(Building $building): void
    {
        $this->buildings->delete($building);
    }
}
