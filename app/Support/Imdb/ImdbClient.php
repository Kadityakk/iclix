<?php

namespace App\Support\Imdb;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Reads IMDb titles through the OMDb API (https://www.omdbapi.com), which needs a free API key.
 */
class ImdbClient
{
    /**
     * Searches are cached because the free OMDb plan allows 1,000 requests a day.
     */
    private const int SEARCH_CACHE_SECONDS = 86400;

    /**
     * OMDb answers "Too many results." for shorter terms.
     */
    public const int MIN_SEARCH_LENGTH = 3;

    public function isConfigured(): bool
    {
        return filled(config('services.omdb.key'));
    }

    /**
     * @throws ImdbUnavailableException
     */
    public function find(string $imdbId): ImdbTitle
    {
        $payload = $this->request(['i' => $imdbId, 'plot' => 'short']);

        if (($payload['Response'] ?? 'False') !== 'True') {
            throw new ImdbUnavailableException($payload['Error'] ?? "Judul {$imdbId} tidak ditemukan di IMDb.");
        }

        return ImdbTitle::fromOmdb($payload);
    }

    /**
     * Search IMDb films by title. Returns nothing rather than failing, so a search page never breaks on IMDb.
     *
     * @return list<ImdbSearchResult>
     */
    public function searchMovies(string $term): array
    {
        $term = mb_strtolower(trim($term));

        if (! $this->isConfigured() || mb_strlen($term) < self::MIN_SEARCH_LENGTH) {
            return [];
        }

        $hits = Cache::remember('imdb:search:'.md5($term), self::SEARCH_CACHE_SECONDS, function () use ($term) {
            try {
                return $this->request(['s' => $term, 'type' => 'movie'])['Search'] ?? [];
            } catch (ImdbUnavailableException) {
                return null;
            }
        }) ?? [];

        return array_map(ImdbSearchResult::fromOmdb(...), $hits);
    }

    /**
     * @param  array<string, string>  $query
     * @return array<string, mixed>
     *
     * @throws ImdbUnavailableException
     */
    private function request(array $query): array
    {
        if (! $this->isConfigured()) {
            throw new ImdbUnavailableException('OMDB_API_KEY belum diisi di .env.');
        }

        try {
            return Http::baseUrl(config('services.omdb.url'))
                ->connectTimeout(3)
                ->timeout(8)
                ->retry([200, 600], 0, fn (Throwable $exception) => $exception instanceof ConnectionException
                    || ($exception instanceof RequestException && $exception->response->serverError()))
                ->get('/', ['apikey' => config('services.omdb.key')] + $query)
                ->throw()
                ->json() ?? [];
        } catch (ConnectionException|RequestException $exception) {
            throw new ImdbUnavailableException('IMDb tidak bisa dihubungi.', previous: $exception);
        }
    }
}
