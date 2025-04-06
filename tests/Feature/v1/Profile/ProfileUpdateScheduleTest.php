<?php

namespace Tests\Feature\v1\Profile;

use App\Enums\ProfileStatus;
use App\Jobs\Scrape;
use App\Models\Profile;
use App\Services\Scrape\Requests\ScrapeProfileRequest;
use Illuminate\Support\Facades\Queue;

describe('Profile Update Schedule', function () {
    it('should update profiles with likes above 100k', function () {
        Queue::fake();

        Profile::factory()->create([
            'status' => ProfileStatus::COMPLETED,
            'likes' => config('scraping.likes_threshold') - 1,
            'last_scraped_at' => now()->subHours(config('scraping.scrape_interval.up_to_100k')),
        ]);

        $profile = Profile::factory()->create([
            'status' => ProfileStatus::COMPLETED,
            'likes' => config('scraping.likes_threshold'),
            'last_scraped_at' => now()->subHours(config('scraping.scrape_interval.above_100k')),
        ]);

        $this->artisan('scrape:profiles', ['--likes-min' => config('scraping.likes_threshold')])->assertSuccessful();

        Queue::assertCount(1);
        Queue::assertPushed(Scrape::class, function (Scrape $job) use ($profile) {
            return $job->request instanceof ScrapeProfileRequest && $job->request->username === $profile->username;
        });
    });

    it('should update profiles with likes up to 100k', function () {
        Queue::fake();

        $profile = Profile::factory()->create([
            'status' => ProfileStatus::COMPLETED,
            'likes' => config('scraping.likes_threshold') - 1,
            'last_scraped_at' => now()->subHours(config('scraping.scrape_interval.up_to_100k')),
        ]);

        Profile::factory()->create([
            'status' => ProfileStatus::COMPLETED,
            'likes' => config('scraping.likes_threshold'),
            'last_scraped_at' => now()->subHours(config('scraping.scrape_interval.above_100k')),
        ]);

        $this->artisan('scrape:profiles', ['--likes-max' => config('scraping.likes_threshold')])->assertSuccessful();

        Queue::assertCount(1);
        Queue::assertPushed(Scrape::class, function (Scrape $job) use ($profile) {
            return $job->request instanceof ScrapeProfileRequest && $job->request->username === $profile->username;
        });
    });
});
