<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureProfileIsSelected;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProfileSelectionController extends Controller
{
    /**
     * Remember which profile is watching for the rest of the session.
     */
    public function __invoke(Request $request, Profile $profile): RedirectResponse
    {
        Gate::authorize('manage', $profile);

        $request->session()->put(EnsureProfileIsSelected::SESSION_KEY, $profile->id);

        return redirect()->intended(route('browse'));
    }
}
