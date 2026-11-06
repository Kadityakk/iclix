<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Episode;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StreamController extends Controller
{
    /**
     * Stream a private video; the file response answers Range requests with 206 so seeking works.
     */
    public function __invoke(Content $content, Profile $profile, ?Episode $episode = null): BinaryFileResponse
    {
        abort_unless($content->isVisibleTo($profile), 404);
        abort_if($episode !== null && ! $episode->belongsToContent($content), 404);

        $path = $content->isMovie() && $episode === null ? $content->video_path : $episode?->video_path;
        $disk = Storage::disk('local');

        abort_if($path === null || ! $disk->exists($path), 404);

        return response()->file($disk->path($path), [
            'Content-Type' => 'video/mp4',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
