<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgressRequest;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;

class ProgressController extends Controller
{
    /**
     * Save how far the profile has watched a video.
     */
    public function store(ProgressRequest $request, Profile $profile): JsonResponse
    {
        $duration = $request->integer('duration_sec');

        $profile->watchHistories()->updateOrCreate(
            [
                'content_id' => $request->integer('content_id'),
                'episode_id' => $request->filled('episode_id') ? $request->integer('episode_id') : null,
            ],
            [
                'position_sec' => min($request->integer('position_sec'), $duration),
                'duration_sec' => $duration,
            ],
        );

        return response()->json(['saved' => true]);
    }
}
