<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Seed the three subscription plans.
     */
    public function run(): void
    {
        $plans = [
            ['name' => 'Basic', 'price' => 39000, 'quality' => '480p', 'max_screens' => 1],
            ['name' => 'Standar', 'price' => 69000, 'quality' => '1080p', 'max_screens' => 2],
            ['name' => 'Premium', 'price' => 119000, 'quality' => '4K', 'max_screens' => 4],
        ];

        foreach ($plans as $plan) {
            Plan::query()->updateOrCreate(['name' => $plan['name']], $plan + ['duration_days' => 30]);
        }
    }
}
