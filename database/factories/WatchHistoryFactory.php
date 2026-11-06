<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\Profile;
use App\Models\WatchHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WatchHistory>
 */
class WatchHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'content_id' => Content::factory(),
            'episode_id' => null,
            'position_sec' => 120,
            'duration_sec' => 600,
        ];
    }
}
