<?php

namespace App\Services\Scrape\Requests;

use App\Data\Scrape\ScrapeDTO;
use App\Enums\ProfileStatus;
use App\Services\ProfileService;
use App\Services\Scrape\Pages\Page;
use App\Services\Scrape\Pages\Profile as ProfilePage;
use Throwable;

class ScrapeProfileRequest implements ScrapeRequest
{
    public function __construct(
        public string $username,
    ) {}

    public function getPage(): Page
    {
        return new ProfilePage($this->username);
    }

    public function processing(): void
    {
        $service = resolve(ProfileService::class);
        $service->update($this->username, ['status' => ProfileStatus::PROCESSING]);
    }

    public function completed(ScrapeDTO $result): void
    {
        /** @var ProfileDTO $profile */
        $profile = $result->content;

        $service = resolve(ProfileService::class);
        $service->update($this->username, [
            'name' => $profile->name,
            'bio' => $profile->bio,
            'likes' => $profile->likes,
            'status' => ProfileStatus::COMPLETED,
            'last_scraped_at' => now(),
        ]);
    }

    public function failed(Throwable $e): void
    {
        $service = resolve(ProfileService::class);
        $service->update($this->username, ['status' => ProfileStatus::FAILED]);
    }
}
