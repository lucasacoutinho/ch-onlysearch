<?php

namespace Tests\Feature\v1;

use App\Enums\ProfileStatus;
use App\Jobs\Scrape;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Mockery\MockInterface;
use Spatie\Browsershot\Browsershot;

describe('Profile', function () {
    it('should dispatch a scraping task', function () {
        Queue::fake();

        $username = 'mocked';

        $response = $this->post(route('v1.profile.scrape', ['username' => $username]));

        $response->assertStatus(200);

        Queue::assertPushed(Scrape::class);

        $this->assertDatabaseHas('profiles', [
            'username' => $username,
            'status'   => ProfileStatus::PENDING,
        ]);
    });

    it('should not scrap a profile before low likes threshold', function () {
        Queue::fake();

        $username = 'mocked';

        Profile::factory()->create([
            'username' => $username,
            'status' => ProfileStatus::COMPLETED,
            'likes' => config('scraping.likes_threshold') - 1,
            'last_scraped_at' => now()->subHours(config('scraping.scrape_interval.up_to_100k') - 1),
        ]);

        $response = $this->post(route('v1.profile.scrape', ['username' => $username]));

        $response->assertStatus(200);

        Queue::assertNotPushed(Scrape::class);

        $this->assertDatabaseHas('profiles', [
            'username' => $username,
            'status'   => ProfileStatus::COMPLETED,
        ]);
    });

    it('should scrape a profile with low likes threshold and not scraped before the scrape interval', function () {
        Queue::fake();

        $username = 'mocked';

        Profile::factory()->create([
            'username' => $username,
            'status' => ProfileStatus::COMPLETED,
            'likes' => config('scraping.likes_threshold') - 1,
            'last_scraped_at' => now()->subHours(config('scraping.scrape_interval.up_to_100k')),
        ]);

        $response = $this->post(route('v1.profile.scrape', ['username' => $username]));

        $response->assertStatus(200);

        Queue::assertPushed(Scrape::class);
    });

    it('should not scrap a profile before high likes threshold', function () {
        Queue::fake();

        $username = 'mocked';

        Profile::factory()->create([
            'username' => $username,
            'status' => ProfileStatus::COMPLETED,
            'likes' => config('scraping.likes_threshold'),
            'last_scraped_at' => now()->subHours(config('scraping.scrape_interval.above_100k') - 1),
        ]);

        $response = $this->post(route('v1.profile.scrape', ['username' => $username]));

        $response->assertStatus(200);

        Queue::assertNotPushed(Scrape::class);

        $this->assertDatabaseHas('profiles', [
            'username' => $username,
            'status'   => ProfileStatus::COMPLETED,
        ]);
    });

    it('should scrape a profile with high likes threshold and not scraped before the scrape interval', function () {
        Queue::fake();

        $username = 'mocked';

        Profile::factory()->create([
            'username' => $username,
            'status' => ProfileStatus::COMPLETED,
            'likes' => config('scraping.likes_threshold'),
            'last_scraped_at' => now()->subHours(config('scraping.scrape_interval.above_100k')),
        ]);

        $response = $this->post(route('v1.profile.scrape', ['username' => $username]));

        $response->assertStatus(200);

        Queue::assertPushed(Scrape::class);
    });

    it('should scrape a profile', function () {
        Carbon::setTestNow();

        $username = 'mocked';

        $this->instance(
            Browsershot::class,
            Mockery::mock(Browsershot::class, function (MockInterface $mock) use ($username) {
                $mock->shouldReceive('url')->with('https://www.onlyfans.com/' . $username)->andReturnSelf();
                $mock->shouldReceive('noSandbox')->andReturnSelf();
                $mock->shouldReceive('waitUntilNetworkIdle')->andReturnSelf();
                $mock->shouldReceive('bodyHtml')->andReturn(file_get_contents(base_path('tests/Mocks/Profile/profile.html')));
            })
        );

        $response = $this->post(route('v1.profile.scrape', ['username' => $username]));

        $response->assertStatus(200);

        $this->assertDatabaseHas('profiles', [
            'username'        => $username,
            'name'            => ucfirst($username),
            'bio'             => 'You know who I am. I know why you’re here. Let\'s Cut the Bullshit. Hit Subscribe.ONLYFANS DISCLAIMER & LEGAL NOTICE: All content on this account is owned by me and protected under US and international copyright law, including any content purchased separately. Reproduction and distribution of any content, comments, conversations and/or private messages is strictly prohibited for any use. Violation of these terms will result in legal action against you and a permanent ban from the OnlyFans platform. By subscribing to this account and engaging in any "Fan and Creator Interactions" you acknowledge full agreement to OnlyFans\' Terms of Service, including the Contract Between Fan and Creator. You further acknowledge and agree that this account is run by me and my team, who are authorized to respond on my behalf to any communications to ensure the best experience. We strive to interact with you in the most authentic and respectful manner and ask that you do the same. Copyright 2025',
            'likes'           => 1_670_000,
            'status'          => ProfileStatus::COMPLETED,
            'last_scraped_at' => now()
        ]);
    });
});
