<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GenreRequest;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GenreController extends Controller
{
    /**
     * List the genres.
     */
    public function index(): View
    {
        return view('admin.genres.index', [
            'genres' => Genre::query()->withCount('contents')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a genre.
     */
    public function create(): View
    {
        return view('admin.genres.form', ['genre' => new Genre]);
    }

    /**
     * Store a new genre.
     */
    public function store(GenreRequest $request): RedirectResponse
    {
        Genre::create($request->validated());

        return redirect()->route('admin.genres.index')->with('status', 'Genre ditambahkan.');
    }

    /**
     * Show the form for editing a genre.
     */
    public function edit(Genre $genre): View
    {
        return view('admin.genres.form', ['genre' => $genre]);
    }

    /**
     * Update a genre.
     */
    public function update(GenreRequest $request, Genre $genre): RedirectResponse
    {
        $genre->update($request->validated());

        return redirect()->route('admin.genres.index')->with('status', 'Genre diperbarui.');
    }

    /**
     * Delete a genre.
     */
    public function destroy(Genre $genre): RedirectResponse
    {
        $genre->delete();

        return redirect()->route('admin.genres.index')->with('status', 'Genre dihapus.');
    }
}
