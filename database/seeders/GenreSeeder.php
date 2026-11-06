<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
    /**
     * Seed the ten catalog genres.
     */
    public function run(): void
    {
        $names = ['Aksi', 'Animasi', 'Drama', 'Fantasi', 'Horor', 'Keluarga', 'Komedi', 'Romansa', 'Sci-Fi', 'Thriller'];

        foreach ($names as $name) {
            Genre::query()->updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
