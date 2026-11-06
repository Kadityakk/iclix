<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    private const int MAX_RESULTS = 24;

    /**
     * Search titles: JSON for the live search, a full page otherwise.
     */
    public function __invoke(Request $request, Profile $profile): View|JsonResponse
    {
        $term = trim($request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? '');
        $results = $term === '' ? new Collection : Content::query()
            ->visibleTo($profile)
            ->titleMatches($term)
            ->with('genres')
            ->orderByDesc('views')
            ->limit(self::MAX_RESULTS)
            ->get();

        if ($request->expectsJson()) {
            return response()->json(['results' => $results->map->toCardData()->values()]);
        }

        return view('search', ['term' => $term, 'results' => $results]);
    }
}
