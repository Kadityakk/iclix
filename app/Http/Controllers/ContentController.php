<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class ContentController extends Controller
{
    private const int SIMILAR_SIZE = 12;

    /**
     * Show a title's detail page.
     */
    public function show(Content $content, Profile $profile): View
    {
        abort_unless($content->isVisibleTo($profile), 404);

        $content->load(['genres', 'seasons.episodes']);
        $genreIds = $content->genres->pluck('id');

        return view('contents.show', [
            'content' => $content,
            'rating' => $profile->ratings()->whereBelongsTo($content)->value('value'),
            'similar' => Content::query()
                ->visibleTo($profile)
                ->whereKeyNot($content->id)
                ->whereHas('genres', fn (Builder $query) => $query->whereKey($genreIds))
                ->orderByDesc('views')
                ->limit(self::SIMILAR_SIZE)
                ->get(),
        ]);
    }
}
