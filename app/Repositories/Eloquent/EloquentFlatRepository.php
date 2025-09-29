<?php

namespace App\Repositories\Eloquent;

use App\Models\Flat;
use App\Repositories\FlatRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentFlatRepository implements FlatRepositoryInterface
{
    public function queryForUser(int $userId, bool $isAdmin): Builder
    {
        return $isAdmin
            ? Flat::query()->with('building')
            : Flat::forOwner($userId)->with('building');
    }

    public function paginateForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator
    {
        return $this->queryForUser($userId, $isAdmin)->paginate($perPage);
    }

    public function create(array $data): Flat
    {
        return Flat::create($data);
    }

    public function update(Flat $flat, array $data): bool
    {
        return $flat->update($data);
    }

    public function delete(Flat $flat): ?bool
    {
        return $flat->delete();
    }
}
