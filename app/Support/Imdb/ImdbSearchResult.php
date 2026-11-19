<?php

namespace App\Support\Imdb;

use App\Enums\ContentType;

/**
 * One hit from an IMDb title search. It only carries what a search result card shows.
 */
final readonly class ImdbSearchResult
{
    public function __construct(
        public string $imdbId,
        public string $title,
        public ?int $year,
        public ?string $posterUrl,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromOmdb(array $payload): self
    {
        $poster = ($payload['Poster'] ?? 'N/A') !== 'N/A' ? (string) $payload['Poster'] : null;

        return new self(
            imdbId: (string) $payload['imdbID'],
            title: (string) $payload['Title'],
            year: preg_match('/^\d{4}/', (string) ($payload['Year'] ?? ''), $matches) ? (int) $matches[0] : null,
            posterUrl: ImdbTitle::resizePoster($poster),
        );
    }

    /**
     * Same shape as Content::toCardData() for the fields the search UI reads. Opening it imports the title.
     *
     * @return array{id: string, type: string, title: string, year: ?int, poster: ?string, rating: null, url: string}
     */
    public function toCardData(): array
    {
        return [
            'id' => $this->imdbId,
            'type' => ContentType::Movie->label(),
            'title' => $this->title,
            'year' => $this->year,
            'poster' => $this->posterUrl,
            'rating' => null,
            'url' => route('imdb.import', $this->imdbId),
        ];
    }
}
