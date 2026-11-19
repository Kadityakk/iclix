<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Profile;
use App\Support\Imdb\ImdbClient;
use App\Support\Imdb\ImdbSearchResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    private const int MAX_RESULTS = 24;

    /**
     * Search titles: catalog matches first, then IMDb films not in the catalog yet.
     * JSON for the live search, a full page otherwise.
     */
    public function __invoke(Request $request, Profile $profile, ImdbClient $imdb): View|JsonResponse
    {
        $term = trim($request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? '');
        $results = $term === '' ? [] : $this->search($term, $profile, $imdb);

        if ($request->expectsJson()) {
            return response()->json(['results' => $results]);
        }

        return view('search', ['term' => $term, 'results' => $results]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function search(string $term, Profile $profile, ImdbClient $imdb): array
    {
        /** Kids profiles only see the catalog, because IMDb search hits carry no age rating. */
        $imdbHits = collect($profile->is_kids ? [] : $imdb->searchMovies($term));

        $catalog = Content::query()
            ->visibleTo($profile)
            ->where(fn (Builder $query) => $query
                ->titleMatches($term)
                ->when($imdbHits->isNotEmpty(), fn (Builder $query) => $query->orWhereIn('imdb_id', $imdbHits->pluck('imdbId'))))
            ->with('genres')
            ->orderByDesc('views')
            ->limit(self::MAX_RESULTS)
            ->get();

        $knownImdbIds = Content::query()->whereIn('imdb_id', $imdbHits->pluck('imdbId'))->pluck('imdb_id');

        return $catalog->map->toCardData()
            ->concat($imdbHits
                ->reject(fn (ImdbSearchResult $hit) => $knownImdbIds->contains($hit->imdbId))
                ->map->toCardData())
            ->take(self::MAX_RESULTS)
            ->values()
            ->all();
    }
}
