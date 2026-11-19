<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Support\Imdb\ImdbSync;
use App\Support\Imdb\ImdbUnavailableException;
use Illuminate\Http\RedirectResponse;

class ImdbImportController extends Controller
{
    /**
     * Open an IMDb film from search, adding it to the catalog the first time someone opens it.
     */
    public function __invoke(string $imdbId, Profile $profile, ImdbSync $imdb): RedirectResponse
    {
        try {
            $content = $imdb->import($imdbId);
        } catch (ImdbUnavailableException $exception) {
            return redirect()->route('search')->with('error', "Film tidak bisa dibuka. {$exception->getMessage()}");
        }

        abort_unless($content->isVisibleTo($profile), 404);

        return redirect()->route('contents.show', $content);
    }
}
