<?php

namespace Tests\Unit\v1\Profile;

use App\Enums\ProfileStatus;
use App\Exceptions\Scrape\ExpectedFieldNotFoundException;
use App\Models\Profile;
use App\Repositories\ProfileRepositoryInterface;
use App\Services\ProfileService;
use Mockery;
use Mockery\MockInterface;
use Spatie\Browsershot\Browsershot;

describe('Profile Scraping', function () {
    it('should scrape a profile successfully', function () {
        $username = 'mocked';

        $this->instance(
            Browsershot::class,
            Mockery::mock(Browsershot::class, function (MockInterface $mock) use ($username) {
                $mock->shouldReceive('setUrl')->with('https://www.onlyfans.com/' . $username)->andReturnSelf();
                $mock->shouldReceive('noSandbox')->andReturnSelf();
                $mock->shouldReceive('waitUntilNetworkIdle')->andReturnSelf();
                $mock->shouldReceive('bodyHtml')->andReturn(file_get_contents(base_path('tests/Mocks/Profile/profile.html')));
            })
        );

        $profileRepository = Mockery::mock(ProfileRepositoryInterface::class);

        $profileRepository->shouldReceive('findByUsername')->with($username)->andReturn(Profile::factory()->create([
            'username' => $username,
        ]));

        $service = new ProfileService($profileRepository);

        $service->scrape($username);

        $this->assertDatabaseHas('profiles', [
            'username' => $username,
            'status' => ProfileStatus::COMPLETED,
        ]);
    });

    it('should fail to scrape a profile', function () {
        $username = 'mocked';

        $this->instance(
            Browsershot::class,
            Mockery::mock(Browsershot::class, function (MockInterface $mock) use ($username) {
                $mock->shouldReceive('setUrl')->with('https://www.onlyfans.com/' . $username)->andReturnSelf();
                $mock->shouldReceive('noSandbox')->andReturnSelf();
                $mock->shouldReceive('waitUntilNetworkIdle')->andReturnSelf();
                $mock->shouldReceive('bodyHtml')->andReturn(file_get_contents(base_path('tests/Mocks/Profile/notfound.html')));
            })
        );

        $profileRepository = Mockery::mock(ProfileRepositoryInterface::class);

        $profileRepository->shouldReceive('findByUsername')->with($username)->andReturn(Profile::factory()->create([
            'username' => $username,
        ]));

        $service = new ProfileService($profileRepository);

        $service->scrape($username);

        $this->assertDatabaseHas('profiles', [
            'username' => $username,
            'status' => ProfileStatus::FAILED,
        ]);
    })->throws(ExpectedFieldNotFoundException::class);
});
