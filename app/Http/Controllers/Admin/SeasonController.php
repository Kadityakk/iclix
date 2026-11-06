<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SeasonRequest;
use App\Models\Content;
use App\Models\Season;
use App\Support\ContentMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SeasonController extends Controller
{
    /**
     * List the seasons of a series.
     */
    public function index(Content $content): View
    {
        abort_unless($content->isSeries(), 404);

        return view('admin.seasons.index', [
            'content' => $content,
            'seasons' => $content->seasons()->withCount('episodes')->get(),
        ]);
    }

    /**
     * Show the form for creating a season.
     */
    public function create(Content $content): View
    {
        abort_unless($content->isSeries(), 404);

        return view('admin.seasons.form', [
            'content' => $content,
            'season' => new Season(['season_number' => (int) $content->seasons()->max('season_number') + 1]),
        ]);
    }

    /**
     * Store a new season.
     */
    public function store(SeasonRequest $request, Content $content): RedirectResponse
    {
        abort_unless($content->isSeries(), 404);

        $content->seasons()->create($request->validated());

        return redirect()->route('admin.contents.seasons.index', $content)->with('status', 'Musim ditambahkan.');
    }

    /**
     * Show the form for editing a season.
     */
    public function edit(Season $season): View
    {
        return view('admin.seasons.form', ['content' => $season->content, 'season' => $season]);
    }

    /**
     * Update a season.
     */
    public function update(SeasonRequest $request, Season $season): RedirectResponse
    {
        $season->update($request->validated());

        return redirect()->route('admin.contents.seasons.index', $season->content_id)->with('status', 'Musim diperbarui.');
    }

    /**
     * Delete a season, its episodes and their files.
     */
    public function destroy(Season $season, ContentMedia $media): RedirectResponse
    {
        foreach ($season->episodes as $episode) {
            $media->deleteImage($episode->thumbnail);
            $media->deleteVideo($episode->video_path);
        }

        $season->delete();

        return redirect()->route('admin.contents.seasons.index', $season->content_id)->with('status', 'Musim dihapus.');
    }
}
