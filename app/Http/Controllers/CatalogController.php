<?php

namespace App\Http\Controllers;

use App\Enums\ContentType;
use App\Models\Content;
use App\Models\Genre;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    private const int PER_PAGE = 24;

    public function movies(Request $request, Profile $profile): View
    {
        return $this->catalog($request, $profile, ContentType::Movie, 'Film');
    }

    public function series(Request $request, Profile $profile): View
    {
        return $this->catalog($request, $profile, ContentType::Series, 'Serial');
    }

    /**
     * Show the most watched titles.
     */
    public function trending(Profile $profile): View
    {
        return view('catalog.index', [
            'title' => 'Trending',
            'contents' => Content::query()->visibleTo($profile)->orderByDesc('views')->paginate(self::PER_PAGE),
            'genres' => null,
            'years' => null,
        ]);
    }

    /**
     * Show every genre that has something the profile may watch.
     */
    public function genres(Profile $profile): View
    {
        $visibleContent = fn (Builder $query) => $query->visibleTo($profile);

        return view('catalog.genres', [
            'genres' => Genre::query()
                ->whereHas('contents', $visibleContent)
                ->withCount(['contents' => $visibleContent])
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Show one content type, optionally filtered by genre and release year.
     */
    private function catalog(Request $request, Profile $profile, ContentType $type, string $title): View
    {
        $filters = $request->validate([
            'genre' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer'],
        ]);

        $baseQuery = Content::query()->where('type', $type)->visibleTo($profile);

        return view('catalog.index', [
            'title' => $title,
            'contents' => (clone $baseQuery)
                ->when($filters['genre'] ?? null, fn (Builder $query, string $slug) => $query->whereHas('genres', fn (Builder $genres) => $genres->where('slug', $slug)))
                ->when($filters['year'] ?? null, fn (Builder $query, int|string $year) => $query->where('release_year', $year))
                ->latest('release_date')
                ->latest('id')
                ->paginate(self::PER_PAGE)
                ->withQueryString(),
            'genres' => Genre::query()->orderBy('name')->get(),
            'years' => (clone $baseQuery)->distinct()->orderByDesc('release_year')->pluck('release_year'),
        ]);
    }
}
