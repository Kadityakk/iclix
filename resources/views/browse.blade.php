<x-app-layout title="Beranda">
    @if ($featured->isNotEmpty())
        <x-hero-slider :items="$featured->map->toCardData()->values()" :liked-content-ids="$likedContentIds" />
    @else
        <div class="px-6 pt-28 pb-6">
            <h1 class="text-3xl font-bold">Selamat menonton</h1>
        </div>
    @endif

    <div class="relative mt-8 space-y-10">
        @if ($continueWatching->isNotEmpty())
            <x-content-row title="Lanjutkan Menonton">
                @foreach ($continueWatching as $history)
                    <a href="{{ $history->episode ? route('watch', [$history->content, $history->episode]) : route('watch', $history->content) }}"
                       class="group w-36 shrink-0 snap-start space-y-2 sm:w-44 lg:w-48">
                        <span class="relative block aspect-[2/3] overflow-hidden rounded-xl bg-card">
                            @if ($history->content->posterUrl())
                                <img src="{{ $history->content->posterUrl() }}" alt="Poster {{ $history->content->title }}" loading="lazy" class="size-full object-cover">
                            @endif
                            <span class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 transition group-hover:opacity-100">
                                <span class="flex size-12 items-center justify-center rounded-full bg-white text-black"><x-icon name="play" /></span>
                            </span>
                            <span class="absolute inset-x-0 bottom-0 h-1 bg-white/25" role="progressbar" aria-valuenow="{{ $history->progressPercent() }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progres tonton">
                                <span class="block h-full bg-white" style="width: {{ $history->progressPercent() }}%"></span>
                            </span>
                        </span>
                        <span class="block truncate text-sm font-medium">{{ $history->content->title }}</span>
                        @if ($history->episode)
                            <span class="block truncate text-xs text-muted">{{ $history->episode->title }}</span>
                        @endif
                    </a>
                @endforeach
            </x-content-row>
        @endif

        <x-content-row title="Trending" id="trending">
            @foreach ($trending as $content)
                <x-poster-card :content="$content" />
            @endforeach
        </x-content-row>

        <x-content-row title="Baru di iClix">
            @foreach ($newest as $content)
                <x-poster-card :content="$content" />
            @endforeach
        </x-content-row>

        @foreach ($genreRows as $genre)
            <x-content-row :title="$genre->name">
                @foreach ($genre->contents as $content)
                    <x-poster-card :content="$content" />
                @endforeach
            </x-content-row>
        @endforeach
    </div>
</x-app-layout>
