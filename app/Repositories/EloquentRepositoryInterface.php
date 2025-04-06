<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface EloquentRepositoryInterface
{
    /**
     * Get all models.
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get all trashed models.
     */
    public function allTrashed(): Collection;

    /**
     * Find model by id.
     */
    public function findById(
        string $modelId,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): ?Model;

    /**
     * Find trashed model by id.
     */
    public function findTrashedById(string $modelId): ?Model;

    /**
     * Find only trashed model by id.
     */
    public function findOnlyTrashedById(string $modelId): ?Model;

    /**
     * Create a model.
     */
    public function create(array $payload): ?Model;

    /**
     * Update existing model.
     */
    public function update(string $modelId, array $payload): bool;

    /**
     * Delete model by id.
     */
    public function deleteById(string $modelId): bool;

    /**
     * Restore model by id.
     */
    public function restoreById(string $modelId): bool;

    /**
     * Permanently delete model by id.
     */
    public function permanentlyDeleteById(string $modelId): bool;
}
