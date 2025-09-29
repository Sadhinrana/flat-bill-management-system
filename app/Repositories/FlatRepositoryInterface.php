<?php

namespace App\Repositories;

use App\Models\Flat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FlatRepositoryInterface
{
    public function queryForUser(int $userId, bool $isAdmin);

    public function paginateForUser(int $userId, bool $isAdmin, int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Flat;

    public function update(Flat $flat, array $data): bool;

    public function delete(Flat $flat): ?bool;
}
