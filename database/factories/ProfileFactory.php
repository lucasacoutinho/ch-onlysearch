<?php

namespace Database\Factories;

use App\Enums\ProfileStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->userName(),
            'name' => fake()->name(),
            'bio' => fake()->text(),
            'likes' => fake()->numberBetween(1000, 1000000),
            'status' => ProfileStatus::PENDING,
        ];
    }
}
