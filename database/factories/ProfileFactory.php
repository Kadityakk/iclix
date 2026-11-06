<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
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
            'user_id' => User::factory(),
            'name' => fake()->firstName(),
            'avatar' => fake()->randomElement(array_keys(Profile::AVATARS)),
            'is_kids' => false,
        ];
    }

    /**
     * Indicate that the profile is a kids profile.
     */
    public function kids(): static
    {
        return $this->state(fn (array $attributes) => ['is_kids' => true]);
    }
}
