<?php

namespace App\Repositories\Eloquent;

use App\Models\Building;
use App\Repositories\BuildingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentBuildingRepository implements BuildingRepositoryInterface
{
    public function queryForUser(int $userId, bool $isAdmin): Builder
    {
        return $isAdmin
            ? Building::query()->with(['owner', 'flats', 'tenants'])
            : Building::forOwner($userId)->with(['owner', 'flats', 'tenants']);
    }

    public function paginateForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator
    {
        return $this->queryForUser($userId, $isAdmin)->paginate($perPage);
    }

    public function create(array $data): Building
    {
        return Building::create($data);
    }

    public function update(Building $building, array $data): bool
    {
        return $building->update($data);
    }

    public function delete(Building $building): ?bool
    {
        return $building->delete();
    }
}
