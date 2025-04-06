<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\LazyCollection;

interface ProfileRepositoryInterface extends EloquentRepositoryInterface
{
    public function search(string $query): Collection;

    public function findByUsername(string $username): ?Model;

    public function updateByUsername(string $username, array $data): void;

    public function getProfilesBetweenLikes(?int $min = null, ?int $max = null): LazyCollection;
}
