<?php

namespace App\Services;

use App\Models\Flat;
use App\Repositories\FlatRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FlatService
{
    public function __construct(private FlatRepositoryInterface $flats)
    {
    }

    public function listForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator
    {
        return $this->flats->paginateForUser($userId, $isAdmin, $perPage);
    }

    public function create(array $data): Flat
    {
        return $this->flats->create($data);
    }

    public function update(Flat $flat, array $data): bool
    {
        return $this->flats->update($flat, $data);
    }

    public function delete(Flat $flat): void
    {
        $this->flats->delete($flat);
    }
}
