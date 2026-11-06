<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class WatchlistController extends Controller
{
    /**
     * Show the profile's saved titles, newest first.
     */
    public function index(Profile $profile): View
    {
        return view('my-list', [
            'contents' => $profile->watchlistContents()->visibleTo($profile)->orderByPivot('created_at', 'desc')->get(),
        ]);
    }

    /**
     * Save a title.
     */
    public function store(Content $content, Profile $profile): JsonResponse
    {
        abort_unless($content->isVisibleTo($profile), 404);

        $profile->watchlistContents()->syncWithoutDetaching([$content->id]);

        return response()->json(['saved' => true, 'message' => 'Ditambahkan ke Daftar Saya']);
    }

    /**
     * Remove a saved title.
     */
    public function destroy(Content $content, Profile $profile): JsonResponse
    {
        $profile->watchlistContents()->detach($content->id);

        return response()->json(['saved' => false, 'message' => 'Dihapus dari Daftar Saya']);
    }
}
