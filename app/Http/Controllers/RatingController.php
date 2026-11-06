<?php

namespace App\Http\Controllers;

use App\Http\Requests\RatingRequest;
use App\Models\Content;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;

class RatingController extends Controller
{
    /**
     * Set or clear (value 0) the profile's rating of a title.
     */
    public function store(RatingRequest $request, Content $content, Profile $profile): JsonResponse
    {
        abort_unless($content->isVisibleTo($profile), 404);

        $value = $request->integer('value');

        if ($value === 0) {
            $profile->ratings()->whereBelongsTo($content)->delete();
        } else {
            $profile->ratings()->updateOrCreate(['content_id' => $content->id], ['value' => $value]);
        }

        return response()->json(['value' => $value]);
    }
}
