<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EpisodeRequest;
use App\Models\Episode;
use App\Models\Season;
use App\Support\ContentMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EpisodeController extends Controller
{
    public function __construct(private ContentMedia $media) {}

    /**
     * List the episodes of a season.
     */
    public function index(Season $season): View
    {
        return view('admin.episodes.index', [
            'season' => $season->load('content'),
            'episodes' => $season->episodes,
        ]);
    }

    /**
     * Show the form for creating an episode.
     */
    public function create(Season $season): View
    {
        return view('admin.episodes.form', [
            'season' => $season->load('content'),
            'episode' => new Episode(['episode_number' => (int) $season->episodes()->max('episode_number') + 1]),
        ]);
    }

    /**
     * Store a new episode with its uploaded files.
     */
    public function store(EpisodeRequest $request, Season $season): RedirectResponse
    {
        $season->episodes()->create($request->safe()->except(['thumbnail', 'video']) + $this->storeUploads($request));

        return redirect()->route('admin.seasons.episodes.index', $season)->with('status', 'Episode ditambahkan.');
    }

    /**
     * Show the form for editing an episode.
     */
    public function edit(Episode $episode): View
    {
        return view('admin.episodes.form', ['season' => $episode->season->load('content'), 'episode' => $episode]);
    }

    /**
     * Update an episode, replacing any file that was re-uploaded.
     */
    public function update(EpisodeRequest $request, Episode $episode): RedirectResponse
    {
        $previousThumbnail = $episode->thumbnail;
        $previousVideo = $episode->video_path;
        $uploads = $this->storeUploads($request);

        $episode->update($request->safe()->except(['thumbnail', 'video']) + $uploads);

        if (isset($uploads['thumbnail'])) {
            $this->media->deleteImage($previousThumbnail);
        }

        if (isset($uploads['video_path'])) {
            $this->media->deleteVideo($previousVideo);
        }

        return redirect()->route('admin.seasons.episodes.index', $episode->season_id)->with('status', 'Episode diperbarui.');
    }

    /**
     * Delete an episode and its files.
     */
    public function destroy(Episode $episode): RedirectResponse
    {
        $this->media->deleteImage($episode->thumbnail);
        $this->media->deleteVideo($episode->video_path);
        $episode->delete();

        return redirect()->route('admin.seasons.episodes.index', $episode->season_id)->with('status', 'Episode dihapus.');
    }

    /**
     * @return array<string, string>
     */
    private function storeUploads(EpisodeRequest $request): array
    {
        $paths = [];

        if ($request->hasFile('thumbnail')) {
            $paths['thumbnail'] = $this->media->storeImage($request->file('thumbnail'), 'thumbnails');
        }

        if ($request->hasFile('video')) {
            $paths['video_path'] = $this->media->storeVideo($request->file('video'));
        }

        return $paths;
    }
}
