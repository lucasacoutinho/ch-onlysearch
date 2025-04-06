<?php

use App\Enums\ProfileStatus;
use App\Models\Profile;

describe('Profile', function () {
    it('should not return the status of a profile if it does not exist', function () {
        $response = $this->get(route('v1.profile.status', ['username' => 'non-existent']));

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Profile non-existent not found',
        ]);
    });

    it('should return the status of a profile', function () {
        $profile = Profile::factory()->create([
            'status' => fake()->randomElement(ProfileStatus::values()),
        ]);

        $response = $this->get(route('v1.profile.status', ['username' => $profile->username]));

        $response->assertStatus(200);

        $response->assertJson([
            'status' => $profile->status->value,
        ]);
    });
});
