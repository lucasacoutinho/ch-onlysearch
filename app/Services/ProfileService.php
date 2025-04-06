<?php

namespace App\Services;

use App\Jobs\Scrape;
use App\Models\Profile;
use App\Repositories\ProfileRepositoryInterface;
use App\Services\Scrape\Requests\ScrapeProfileRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\LazyCollection;

class ProfileService
{
    public function __construct(
        protected ProfileRepositoryInterface $profileRepository,
    ) {}

    public function search(string $query): Collection
    {
        return $this->profileRepository->search($query);
    }

    public function findByUsername(string $username): ?Profile
    {
        return $this->profileRepository->findByUsername($username);
    }

    public function getProfilesBetweenLikes(?int $min = null, ?int $max = null): LazyCollection
    {
        return $this->profileRepository->getProfilesBetweenLikes($min, $max);
    }

    public function scrape(string $username): void
    {
        $profile = $this->findByUsername($username);

        if ($profile && $profile->was_scraped) {
            return;
        }

        if (! $profile) {
            $profile = $this->profileRepository->create(['username' => $username]);
        }

        Scrape::dispatch(new ScrapeProfileRequest($profile->username))->onQueue($this->getQueue($profile));
    }

    private function getQueue(Profile $profile): string
    {
        return match (true) {
            $profile->likes < 100_000 => 'up_to_100k',
            $profile->likes >= 100_000 => 'above_100k',
            default => 'default',
        };
    }

    public function update(string $username, array $data): void
    {
        $this->profileRepository->updateByUsername($username, $data);
    }
}
