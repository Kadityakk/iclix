<?php

namespace Database\Factories;

use App\Enums\AgeRating;
use App\Enums\ContentType;
use App\Models\Content;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Content>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'type' => ContentType::Movie,
            'title' => $title,
            'slug' => Str::slug($title),
            'synopsis' => fake()->paragraph(),
            'release_year' => fake()->numberBetween(2015, 2026),
            'release_date' => fake()->date(),
            'tagline' => fake()->sentence(4),
            'duration_min' => fake()->numberBetween(80, 160),
            'age_rating' => AgeRating::ThirteenPlus,
            'cast_members' => [fake()->name(), fake()->name()],
            'poster' => null,
            'backdrop' => null,
            'trailer_url' => null,
            'video_path' => 'videos/'.Str::random(12).'.mp4',
            'is_featured' => false,
        ];
    }

    /**
     * Indicate that the content is a series; its video lives on episodes.
     */
    public function series(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ContentType::Series,
            'duration_min' => null,
            'video_path' => null,
        ]);
    }

    /**
     * Indicate that the content is safe for kids profiles.
     */
    public function forKids(): static
    {
        return $this->state(fn (array $attributes) => ['age_rating' => AgeRating::AllAges]);
    }

    /**
     * Indicate that the content is restricted to adults.
     */
    public function forAdults(): static
    {
        return $this->state(fn (array $attributes) => ['age_rating' => AgeRating::SeventeenPlus]);
    }

    /**
     * Indicate that the content appears in the hero banner.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true]);
    }
}
