<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContentRequest;
use App\Models\Content;
use App\Models\Genre;
use App\Support\ContentMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(private ContentMedia $media) {}

    /**
     * List content with search and a type filter.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'in:movie,series'],
        ]);

        return view('admin.contents.index', [
            'contents' => Content::query()
                ->when($filters['q'] ?? null, fn (Builder $query, string $term) => $query->titleMatches($term))
                ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
                ->with('genres')
                ->withCount('seasons')
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating content.
     */
    public function create(): View
    {
        return view('admin.contents.form', [
            'content' => new Content(['type' => ContentType::Movie]),
            'genres' => Genre::query()->orderBy('name')->get(),
            'selectedGenreIds' => [],
        ]);
    }

    /**
     * Store new content with its uploaded files.
     */
    public function store(ContentRequest $request): RedirectResponse
    {
        $content = Content::create($request->contentAttributes() + $this->storeUploads($request));
        $content->genres()->sync($request->validated('genres'));

        return redirect()->route($content->isSeries() ? 'admin.contents.seasons.index' : 'admin.contents.index', $content->isSeries() ? $content : [])
            ->with('status', "{$content->title} berhasil ditambahkan.");
    }

    /**
     * Show the form for editing content.
     */
    public function edit(Content $content): View
    {
        return view('admin.contents.form', [
            'content' => $content,
            'genres' => Genre::query()->orderBy('name')->get(),
            'selectedGenreIds' => $content->genres()->pluck('genres.id')->all(),
        ]);
    }

    /**
     * Update content, replacing any file that was re-uploaded.
     */
    public function update(ContentRequest $request, Content $content): RedirectResponse
    {
        $previousFiles = $content->only(['poster', 'backdrop', 'video_path']);
        $uploads = $this->storeUploads($request);

        $content->update($request->contentAttributes() + $uploads);
        $content->genres()->sync($request->validated('genres'));

        foreach (array_keys($uploads) as $field) {
            $field === 'video_path'
                ? $this->media->deleteVideo($previousFiles[$field])
                : $this->media->deleteImage($previousFiles[$field]);
        }

        return redirect()->route('admin.contents.index')->with('status', "{$content->title} berhasil diperbarui.");
    }

    /**
     * Delete content and all of its files.
     */
    public function destroy(Content $content): RedirectResponse
    {
        $this->media->deleteAllFor($content);
        $content->delete();

        return redirect()->route('admin.contents.index')->with('status', "{$content->title} dihapus.");
    }

    /**
     * Toggle whether the content appears in the hero banner.
     */
    public function toggleFeatured(Content $content): RedirectResponse
    {
        $content->update(['is_featured' => ! $content->is_featured]);

        return back()->with('status', $content->is_featured
            ? "{$content->title} tampil di banner hero."
            : "{$content->title} dilepas dari banner hero.");
    }

    /**
     * @return array<string, string>
     */
    private function storeUploads(ContentRequest $request): array
    {
        $paths = [];

        if ($request->hasFile('poster')) {
            $paths['poster'] = $this->media->storeImage($request->file('poster'), 'posters');
        }

        if ($request->hasFile('backdrop')) {
            $paths['backdrop'] = $this->media->storeImage($request->file('backdrop'), 'backdrops');
        }

        if ($request->hasFile('video')) {
            $paths['video_path'] = $this->media->storeVideo($request->file('video'));
        }

        return $paths;
    }
}
