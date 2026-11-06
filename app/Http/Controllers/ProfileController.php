<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureProfileIsSelected;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the "who is watching?" picker.
     */
    public function index(Request $request): View
    {
        return view('profiles.index', [
            'profiles' => $request->user()->profiles()->oldest()->get(),
            'canAddProfile' => ! $request->user()->hasReachedProfileLimit(),
        ]);
    }

    /**
     * Show the form for creating a profile.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasReachedProfileLimit()) {
            return redirect()->route('profiles.index')
                ->with('status', 'Satu akun maksimal punya '.User::MAX_PROFILES.' profil.');
        }

        return view('profiles.form', ['profile' => new Profile(['avatar' => 'violet'])]);
    }

    /**
     * Store a new profile.
     */
    public function store(ProfileRequest $request): RedirectResponse
    {
        $request->user()->profiles()->create($request->profileAttributes());

        return redirect()->route('profiles.index')->with('status', 'Profil ditambahkan.');
    }

    /**
     * Show the form for editing a profile.
     */
    public function edit(Profile $profile): View
    {
        Gate::authorize('manage', $profile);

        return view('profiles.form', ['profile' => $profile]);
    }

    /**
     * Update a profile.
     */
    public function update(ProfileRequest $request, Profile $profile): RedirectResponse
    {
        Gate::authorize('manage', $profile);

        $profile->update($request->profileAttributes());

        return redirect()->route('profiles.index')->with('status', 'Profil diperbarui.');
    }

    /**
     * Delete a profile and forget it if it is the one in use.
     */
    public function destroy(Request $request, Profile $profile): RedirectResponse
    {
        Gate::authorize('manage', $profile);

        if ($request->session()->get(EnsureProfileIsSelected::SESSION_KEY) === $profile->id) {
            $request->session()->forget(EnsureProfileIsSelected::SESSION_KEY);
        }

        $profile->delete();

        return redirect()->route('profiles.index')->with('status', 'Profil dihapus.');
    }
}
