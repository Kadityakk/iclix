<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Genre;
use App\Models\Profile;
use App\Models\WatchHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BrowseController extends Controller
{
    private const int ROW_SIZE = 15;

    private const int GENRE_ROWS = 4;

    private const int CONTINUE_WATCHING_SIZE = 12;

    /**
     * Show the member home page.
     */
    public function __invoke(Profile $profile): View
    {
        return view('browse', [
            'featured' => Content::query()->visibleTo($profile)->featured()->with('genres')->get(),
            'continueWatching' => $this->continueWatching($profile),
            'trending' => Content::query()->visibleTo($profile)->orderByDesc('views')->limit(self::ROW_SIZE)->get(),
            'newest' => Content::query()->visibleTo($profile)->latest('release_date')->latest('id')->limit(self::ROW_SIZE)->get(),
            'genreRows' => $this->genreRows($profile),
            'likedContentIds' => $profile->ratings()->where('value', 1)->pluck('content_id'),
        ]);
    }

    /**
     * Latest unfinished video per title, newest first.
     *
     * @return Collection<int, WatchHistory>
     */
    private function continueWatching(Profile $profile): Collection
    {
        return $profile->watchHistories()
            ->with(['content', 'episode'])
            ->latest('updated_at')
            ->limit(50)
            ->get()
            ->reject(fn (WatchHistory $history) => $history->isFinished())
            ->unique('content_id')
            ->take(self::CONTINUE_WATCHING_SIZE)
            ->values()
            ->toBase();
    }

    /**
     * @return EloquentCollection<int, Genre>
     */
    private function genreRows(Profile $profile): EloquentCollection
    {
        $visibleContent = fn (Builder $query) => $query->visibleTo($profile);

        return Genre::query()
            ->whereHas('contents', $visibleContent)
            ->withCount(['contents' => $visibleContent])
            ->with(['contents' => fn ($query) => $query->visibleTo($profile)->latest('release_date')->limit(self::ROW_SIZE)])
            ->orderByDesc('contents_count')
            ->limit(self::GENRE_ROWS)
            ->get();
    }
}
