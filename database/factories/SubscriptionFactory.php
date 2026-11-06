<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
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
            'plan_id' => Plan::factory(),
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'status' => SubscriptionStatus::Active,
            'amount' => 54000,
            'invoice_code' => 'INV-'.Str::upper(Str::random(12)),
        ];
    }

    /**
     * Indicate that the subscription period has passed.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subDays(40),
            'ends_at' => now()->subDays(10),
            'status' => SubscriptionStatus::Expired,
        ]);
    }
}
