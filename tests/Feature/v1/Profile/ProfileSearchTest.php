<?php

namespace Tests\Feature\v1;

use App\Models\Profile;

describe('Profile', function () {
    beforeEach(function () {
        Profile::factory()->create([
            'username' => 'testing-one',
            'name' => 'Tester One',
            'bio' => 'Hello, i am Tester One',
            'likes' => 100,
        ]);

        Profile::factory()->create([
            'username' => 'testing-two',
            'name' => 'Tester Two',
            'bio' => 'Hello, i am Tester Two',
            'likes' => 10,
        ]);
    });

    it('should return profiles with the same username', function () {
        $response = $this->get(route('v1.profile.index', ['query' => 'test']));

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'username' => 'testing-one',
                    'likes' => 100,
                ],
                [
                    'username' => 'testing-two',
                    'likes' => 10,
                ],
            ],
        ]);
    });

    it('should return profiles with the same name', function () {
        $response = $this->get(route('v1.profile.index', ['query' => 'Testing']));

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'name' => 'Tester One',
                    'likes' => 100,
                ],
                [
                    'name' => 'Tester Two',
                    'likes' => 10,
                ],
            ],
        ]);
    });

    it('should return profiles with the same bio', function () {
        $response = $this->get(route('v1.profile.index', ['query' => 'Hello']));

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'bio' => 'Hello, i am Tester One',
                    'likes' => 100,
                ],
                [
                    'bio' => 'Hello, i am Tester Two',
                    'likes' => 10,
                ],
            ],
        ]);
    });

    it('should return profiles with the same username, name, or bio', function () {
        $response = $this->get(route('v1.profile.index', ['query' => 'est']));

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'username' => 'testing-one',
                    'name' => 'Tester One',
                    'bio' => 'Hello, i am Tester One',
                    'likes' => 100,
                ],
                [
                    'username' => 'testing-two',
                    'name' => 'Tester Two',
                    'bio' => 'Hello, i am Tester Two',
                    'likes' => 10,
                ],
            ],
        ]);
    });
});
