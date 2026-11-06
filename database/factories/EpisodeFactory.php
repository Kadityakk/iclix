<?php

namespace Database\Factories;

use App\Models\Episode;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Episode>
 */
class EpisodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'episode_number' => fake()->unique()->numberBetween(1, 1000),
            'title' => fake()->sentence(3),
            'synopsis' => fake()->sentence(),
            'duration_min' => fake()->numberBetween(20, 60),
            'thumbnail' => null,
            'video_path' => 'videos/'.Str::random(12).'.mp4',
        ];
    }
}
