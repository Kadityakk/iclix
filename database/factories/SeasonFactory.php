<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Season>
 */
class SeasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content_id' => Content::factory()->series(),
            'season_number' => fake()->unique()->numberBetween(1, 1000),
            'title' => null,
        ];
    }
}
