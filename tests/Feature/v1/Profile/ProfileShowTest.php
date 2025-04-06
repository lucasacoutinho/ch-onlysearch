<?php

use App\Enums\ProfileStatus;
use App\Models\Profile;

describe('Profile', function () {
    it('should not return the profile if it does not exist', function () {
        $response = $this->get(route('v1.profile.show', ['username' => 'non-existent']));

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Profile non-existent not found',
        ]);
    });

    it('should return the profile', function () {
        $profile = Profile::factory()->create([
            'status' => fake()->randomElement(ProfileStatus::values()),
        ]);

        $response = $this->get(route('v1.profile.show', ['username' => $profile->username]));

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'username' => $profile->username,
                'name' => $profile->name,
                'bio' => $profile->bio,
                'likes' => $profile->likes,
                'last_scraped_at' => $profile->last_scraped_at,
            ],
        ]);
    });
});
