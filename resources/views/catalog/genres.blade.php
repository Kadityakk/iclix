<x-app-layout title="Genre">
    <div class="space-y-6 px-4 pt-24 sm:px-6">
        <h1 class="text-3xl font-bold">Genre</h1>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($genres as $genre)
                <a href="{{ route('movies.index', ['genre' => $genre->slug]) }}" x-reveal
                   class="rounded-2xl bg-card p-6 transition hover:bg-card-hover">
                    <span class="block text-lg font-semibold">{{ $genre->name }}</span>
                    <span class="text-sm text-muted">{{ $genre->contents_count }} judul</span>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
