<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Episode;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlayerController extends Controller
{
    /**
     * Show the video player, resuming from the profile's last position.
     */
    public function show(Content $content, Profile $profile, ?Episode $episode = null): View|RedirectResponse
    {
        abort_unless($content->isVisibleTo($profile), 404);
        abort_if($content->isMovie() && $episode !== null, 404);
        abort_if($episode !== null && ! $episode->belongsToContent($content), 404);

        $episodes = collect();

        if ($content->isSeries()) {
            $content->load('seasons.episodes');
            $episodes = $content->seasons->flatMap->episodes->values();
            $episode ??= $this->episodeToResume($content, $profile) ?? $episodes->first();

            if ($episode === null) {
                return redirect()->route('contents.show', $content)->with('status', 'Belum ada episode untuk serial ini.');
            }
        }

        $history = $profile->watchHistories()
            ->whereBelongsTo($content)
            ->where('episode_id', $episode?->id)
            ->first();

        $content->incrementQuietly('views');

        $currentIndex = $episode ? $episodes->search(fn (Episode $item) => $item->is($episode)) : false;

        return view('player', [
            'content' => $content,
            'episode' => $episode,
            'nextEpisode' => $currentIndex === false ? null : $episodes->get($currentIndex + 1),
            'resumeFrom' => $history && ! $history->isFinished() ? $history->position_sec : 0,
        ]);
    }

    private function episodeToResume(Content $content, Profile $profile): ?Episode
    {
        return $profile->watchHistories()
            ->whereBelongsTo($content)
            ->whereNotNull('episode_id')
            ->latest('updated_at')
            ->first()
            ?->episode;
    }
}
