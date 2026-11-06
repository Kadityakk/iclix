<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with the demo data.
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            GenreSeeder::class,
            ContentSeeder::class,
            UserSeeder::class,
        ]);
    }
}
