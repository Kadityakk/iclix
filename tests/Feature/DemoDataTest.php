<?php

use App\Models\Content;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Storage;

test('seeding produces the demo data the PRD asks for', function () {
    Storage::fake('public');
    Storage::fake('local');

    $this->seed(DatabaseSeeder::class);

    expect(User::query()->where('email', 'admin@iclix.test')->sole()->isAdmin())->toBeTrue()
        ->and(Plan::query()->count())->toBe(3)
        ->and(Genre::query()->count())->toBe(10)
        ->and(Content::query()->count())->toBeGreaterThanOrEqual(20)
        ->and(Content::query()->where('is_featured', true)->count())->toBeLessThanOrEqual(Content::MAX_FEATURED)
        ->and(Episode::query()->count())->toBeGreaterThan(0);

    $content = Content::query()->first();

    Storage::disk('public')->assertExists([$content->poster, $content->backdrop]);
    Storage::disk('local')->assertExists('demo/sample.mp4');
});

test('seeding twice does not duplicate anything', function () {
    Storage::fake('public');
    Storage::fake('local');

    $this->seed(DatabaseSeeder::class);
    $contentCount = Content::query()->count();
    $userCount = User::query()->count();

    $this->seed(DatabaseSeeder::class);

    expect(Content::query()->count())->toBe($contentCount)
        ->and(User::query()->count())->toBe($userCount);
});
