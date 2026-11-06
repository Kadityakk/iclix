<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    private const string DEMO_VIDEO_PATH = 'demo/sample.mp4';

    private const int SEASONS_PER_SERIES = 2;

    private const int EPISODES_PER_SEASON = 3;

    /**
     * Openly licensed Blender Foundation films, used as placeholder trailers.
     */
    private const array TRAILER_URLS = [
        'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
        'https://www.youtube.com/watch?v=eRsGyueVLvQ',
        'https://www.youtube.com/watch?v=R6MlUcmOul8',
    ];

    /**
     * Seed the demo catalog and publish its artwork and sample video.
     */
    public function run(): void
    {
        Artisan::call('storage:link');

        $demoDirectory = database_path('seeders/demo');
        Storage::disk('local')->put(self::DEMO_VIDEO_PATH, File::get("{$demoDirectory}/sample.mp4"));

        $genreIds = Genre::query()->pluck('id', 'name');
        $catalog = require "{$demoDirectory}/catalog.php";

        foreach ($catalog as $index => $item) {
            $slug = Str::slug($item['title']);
            $posterPath = $this->publishImage("{$demoDirectory}/posters/{$slug}.webp", "demo/posters/{$slug}.webp");
            $backdropPath = $this->publishImage("{$demoDirectory}/backdrops/{$slug}.webp", "demo/backdrops/{$slug}.webp");

            $content = Content::query()->updateOrCreate(['slug' => $slug], [
                'type' => $item['type'],
                'title' => $item['title'],
                'synopsis' => $item['synopsis'],
                'release_year' => $item['year'],
                'release_date' => $item['date'],
                'tagline' => $item['tagline'],
                'duration_min' => $item['duration'],
                'age_rating' => $item['age'],
                'cast_members' => $item['cast'],
                'poster' => $posterPath,
                'backdrop' => $backdropPath,
                'trailer_url' => self::TRAILER_URLS[$index % count(self::TRAILER_URLS)],
                'video_path' => $item['type'] === 'movie' ? self::DEMO_VIDEO_PATH : null,
                'is_featured' => $item['featured'],
            ]);

            $content->forceFill(['views' => $item['views']])->save();
            $content->genres()->sync($genreIds->only($item['genres'])->values());

            if ($content->isSeries()) {
                $this->seedSeasons($content);
            }
        }
    }

    private function publishImage(string $sourcePath, string $targetPath): string
    {
        Storage::disk('public')->put($targetPath, File::get($sourcePath));

        return $targetPath;
    }

    private function seedSeasons(Content $content): void
    {
        foreach (range(1, self::SEASONS_PER_SERIES) as $seasonNumber) {
            $season = $content->seasons()->updateOrCreate(['season_number' => $seasonNumber]);

            foreach (range(1, self::EPISODES_PER_SEASON) as $episodeNumber) {
                $season->episodes()->updateOrCreate(['episode_number' => $episodeNumber], [
                    'title' => "Episode {$episodeNumber}",
                    'synopsis' => "Musim {$seasonNumber}, bagian {$episodeNumber} dari kisah {$content->title}.",
                    'duration_min' => 40 + $episodeNumber,
                    'video_path' => self::DEMO_VIDEO_PATH,
                ]);
            }
        }
    }
}
