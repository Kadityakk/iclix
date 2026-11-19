<?php

namespace App\Support\Imdb;

use App\Enums\AgeRating;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

/**
 * The parts of an IMDb title (as served by OMDb) that the catalog uses.
 */
final readonly class ImdbTitle
{
    /**
     * Width IMDb's image CDN resizes posters to; the original files are several megabytes.
     */
    public const int POSTER_WIDTH = 500;

    /**
     * IMDb genres mapped onto the catalog's Indonesian genre names. Unlisted genres are ignored.
     *
     * @var array<string, string>
     */
    private const array GENRE_NAMES = [
        'Action' => 'Aksi',
        'Adventure' => 'Aksi',
        'Animation' => 'Animasi',
        'Drama' => 'Drama',
        'Fantasy' => 'Fantasi',
        'Horror' => 'Horor',
        'Family' => 'Keluarga',
        'Comedy' => 'Komedi',
        'Romance' => 'Romansa',
        'Sci-Fi' => 'Sci-Fi',
        'Thriller' => 'Thriller',
        'Crime' => 'Thriller',
        'Mystery' => 'Thriller',
    ];

    /**
     * @param  list<string>  $cast
     * @param  list<string>  $genreNames  Catalog genre names, already translated.
     */
    public function __construct(
        public string $imdbId,
        public string $title,
        public bool $isSeries,
        public ?int $year,
        public ?string $plot,
        public ?float $rating,
        public ?string $posterUrl,
        public ?int $runtimeMinutes,
        public ?CarbonImmutable $releaseDate,
        public ?AgeRating $ageRating,
        public array $genreNames,
        public array $cast,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromOmdb(array $payload): self
    {
        $value = fn (string $key): ?string => isset($payload[$key]) && $payload[$key] !== 'N/A' ? (string) $payload[$key] : null;

        return new self(
            imdbId: (string) $payload['imdbID'],
            title: (string) $payload['Title'],
            isSeries: ($payload['Type'] ?? null) === 'series',
            year: preg_match('/^\d{4}/', $value('Year') ?? '', $matches) ? (int) $matches[0] : null,
            plot: $value('Plot'),
            rating: is_numeric($value('imdbRating')) ? (float) $value('imdbRating') : null,
            posterUrl: self::resizePoster($value('Poster')),
            runtimeMinutes: preg_match('/^(\d+)\s*min/', $value('Runtime') ?? '', $matches) ? (int) $matches[1] : null,
            releaseDate: self::parseDate($value('Released')),
            ageRating: AgeRating::fromCertificate($value('Rated')),
            genreNames: self::splitList($value('Genre'))->map(fn (string $genre) => self::GENRE_NAMES[$genre] ?? null)->filter()->unique()->values()->all(),
            cast: self::splitList($value('Actors'))->all(),
        );
    }

    /**
     * Ask IMDb's image CDN for a web-sized copy instead of the original upload.
     */
    public static function resizePoster(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        return preg_replace('/\._V1_[^\/]*\.(jpg|png)$/', '._V1_QL75_UX'.self::POSTER_WIDTH.'_.$1', $url) ?? $url;
    }

    /**
     * @return Collection<int, string>
     */
    private static function splitList(?string $list): Collection
    {
        return Str::of($list ?? '')->explode(',')->map(fn (string $item) => trim($item))->filter()->values();
    }

    private static function parseDate(?string $date): ?CarbonImmutable
    {
        if ($date === null) {
            return null;
        }

        try {
            return CarbonImmutable::createFromFormat('d M Y', $date)->startOfDay();
        } catch (Throwable) {
            return null;
        }
    }
}
