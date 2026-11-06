<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'price' => fake()->numberBetween(30, 180) * 1000,
            'quality' => fake()->randomElement(Plan::QUALITIES),
            'max_screens' => fake()->numberBetween(1, 4),
            'duration_days' => 30,
        ];
    }
}
