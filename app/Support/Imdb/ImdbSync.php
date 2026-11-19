<?php

namespace App\Support\Imdb;

use App\Enums\AgeRating;
use App\Enums\ContentType;
use App\Models\Content;
use App\Models\Genre;
use App\Support\ContentMedia;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;

/**
 * Copies IMDb data onto content records, and adds IMDb films to the catalog on demand.
 *
 * IMDb is the source of truth for everything it knows about a title. The only exceptions are a poster an
 * admin uploaded, and fields IMDb leaves empty, which keep their current value. The slug never changes,
 * so existing links keep working.
 */
class ImdbSync
{
    public function __construct(private ImdbClient $client) {}

    /**
     * @throws ImdbUnavailableException
     */
    public function sync(Content $content): void
    {
        $this->apply($content, $this->client->find($content->imdb_id));
    }

    /**
     * Return the catalog entry for an IMDb film, creating it with the sample video when it is new.
     *
     * @throws ImdbUnavailableException
     */
    public function import(string $imdbId): Content
    {
        $existing = Content::query()->firstWhere('imdb_id', $imdbId);

        if ($existing) {
            return $existing;
        }

        $title = $this->client->find($imdbId);

        if ($title->isSeries) {
            throw new ImdbUnavailableException('Serial belum bisa ditambahkan dari pencarian.');
        }

        $content = new Content([
            'type' => ContentType::Movie,
            'imdb_id' => $imdbId,
            'slug' => $this->uniqueSlug($title),
            'synopsis' => 'Sinopsis belum tersedia.',
            'release_year' => now()->year,
            'age_rating' => AgeRating::ThirteenPlus,
            'video_path' => ContentMedia::SAMPLE_VIDEO_PATH,
        ]);

        try {
            $this->apply($content, $title);
        } catch (UniqueConstraintViolationException) {
            return Content::query()->where('imdb_id', $imdbId)->firstOrFail();
        }

        return $content;
    }

    private function apply(Content $content, ImdbTitle $title): void
    {
        $content->title = $title->title;
        $content->imdb_rating = $title->rating;
        $content->synopsis = $title->plot ?? $content->synopsis;
        $content->release_year = $title->year ?? $content->release_year;
        $content->release_date = $title->releaseDate ?? $content->release_date;
        $content->age_rating = $title->ageRating ?? $content->age_rating;
        $content->cast_members = $title->cast ?: $content->cast_members;

        if ($content->isMovie()) {
            $content->duration_min = $title->runtimeMinutes ?? $content->duration_min;
        }

        if ($title->posterUrl && ($content->poster === null || ContentMedia::isRemote($content->poster))) {
            $content->poster = $title->posterUrl;
        }

        $content->save();

        $genreIds = Genre::query()->whereIn('name', $title->genreNames)->pluck('id');

        if ($genreIds->isNotEmpty()) {
            $content->genres()->sync($genreIds);
        }
    }

    /**
     * Films often share a title ("Venom" 2005 and 2018), so the year and then the IMDb ID break ties.
     */
    private function uniqueSlug(ImdbTitle $title): string
    {
        $candidates = array_filter([
            Str::slug($title->title),
            Str::slug("{$title->title} {$title->year}"),
            Str::slug("{$title->title} {$title->imdbId}"),
        ]);

        foreach ($candidates as $slug) {
            if (! Content::query()->where('slug', $slug)->exists()) {
                return $slug;
            }
        }

        return $title->imdbId;
    }
}
