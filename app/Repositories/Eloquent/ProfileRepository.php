<?php

namespace App\Repositories\Eloquent;

use App\Models\Profile;
use App\Repositories\ProfileRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\LazyCollection;

class ProfileRepository extends BaseRepository implements ProfileRepositoryInterface
{
    protected $model;

    public function __construct(Profile $model)
    {
        $this->model = $model;
    }

    public function search(string $query): Collection
    {
        return $this->model->search($query)->orderByDesc('likes')->get();
    }

    public function findByUsername(string $username): ?Profile
    {
        return $this->model->where('username', $username)->first();
    }

    public function updateByUsername(string $username, array $data): void
    {
        $this->model->where('username', $username)->update($data);
    }

    public function getProfilesBetweenLikes(?int $min = null, ?int $max = null): LazyCollection
    {
        return $this->model->query()
            ->when($min, fn($query) => $query->where('likes', '>=', $min))
            ->when($max, fn($query) => $query->where('likes', '<', $max))
            ->orderBy('likes', 'desc')
            ->cursor();
    }
}
