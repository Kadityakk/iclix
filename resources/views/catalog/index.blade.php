<x-app-layout :title="$title">
    <div class="space-y-6 px-4 pt-24 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <h1 class="text-3xl font-bold">{{ $title }}</h1>

            @if ($genres)
                <form method="GET" class="flex flex-wrap gap-3" x-data @change="$el.submit()" aria-label="Filter katalog">
                    <select name="genre" aria-label="Genre" class="rounded-full border border-white/15 bg-card px-4 py-2 text-sm">
                        <option value="">Semua genre</option>
                        @foreach ($genres as $genre)
                            <option value="{{ $genre->slug }}" @selected(request('genre') === $genre->slug)>{{ $genre->name }}</option>
                        @endforeach
                    </select>
                    <select name="year" aria-label="Tahun" class="rounded-full border border-white/15 bg-card px-4 py-2 text-sm">
                        <option value="">Semua tahun</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" @selected((int) request('year') === $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                    <noscript><button class="rounded-full bg-brand px-4 py-2 text-sm">Terapkan</button></noscript>
                </form>
            @endif
        </div>

        @if ($contents->isEmpty())
            <p class="py-16 text-center text-muted">Tidak ada judul yang cocok dengan filter ini.</p>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @foreach ($contents as $content)
                    <x-poster-card :content="$content" fluid />
                @endforeach
            </div>

            {{ $contents->links() }}
        @endif
    </div>
</x-app-layout>
