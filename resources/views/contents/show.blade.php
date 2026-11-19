<x-app-layout :title="$content->title">
    <section x-data x-init="$store.ratings.seed([{{ $content->id }}], {{ (int) $rating }})"
             class="bg-card px-5 pt-24 pb-8 md:m-4 md:rounded-3xl md:px-12 md:pt-20 md:pb-12">
        <div class="mx-auto flex max-w-6xl flex-col gap-8 md:flex-row md:items-start">
            @if ($content->posterUrl())
                <img src="{{ $content->posterUrl() }}" alt="Poster {{ $content->title }}" class="aspect-[2/3] w-44 shrink-0 rounded-2xl object-cover sm:w-56 lg:w-64">
            @endif

            <div class="max-w-2xl space-y-5">
                <p class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted">
                    <x-imdb-rating :content="$content" />
                    <span>{{ $content->release_year }}</span>
                    @if ($content->formattedDuration())
                        <span>{{ $content->formattedDuration() }}</span>
                    @endif
                    <span class="rounded border border-white/25 px-1.5 text-xs">{{ $content->age_rating->value }}</span>
                    <span>{{ $content->genres->pluck('name')->join(', ') }}</span>
                </p>

                <h1 class="text-4xl leading-tight font-bold tracking-tight md:text-5xl">{{ $content->title }}</h1>

                <p class="leading-relaxed text-white/75">{{ $content->synopsis }}</p>

                @if ($content->cast_members)
                    <p class="text-sm text-muted">{{ implode(', ', $content->cast_members) }}</p>
                @endif

                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <a href="{{ route('watch', $content) }}" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-black transition hover:bg-white/85">
                        <x-icon name="play" class="size-4" /> Tonton
                    </a>
                    @if ($content->trailerEmbedUrl())
                        <button type="button" x-data @click="$store.detail.openTrailer(@js($content->trailerEmbedUrl()))" class="rounded-full border border-white/20 px-6 py-3 text-sm font-semibold transition hover:bg-white/10">Trailer</button>
                    @endif
                    <button type="button" x-data @click="$store.myList.toggle({{ $content->id }})" :aria-pressed="$store.myList.has({{ $content->id }})" aria-label="Simpan ke Daftar Saya"
                            class="flex size-11 items-center justify-center rounded-full border border-white/20 transition hover:bg-white/10">
                        <x-icon name="bookmark" x-bind:fill="$store.myList.has({{ $content->id }}) ? 'currentColor' : 'none'" />
                    </button>
                    <button type="button" x-data @click="$store.ratings.rate({{ $content->id }}, 1)" :aria-pressed="$store.ratings.valueOf({{ $content->id }}) === 1" aria-label="Suka"
                            class="flex size-11 items-center justify-center rounded-full border border-white/20 transition hover:bg-white/10">
                        <x-icon name="like" x-bind:fill="$store.ratings.valueOf({{ $content->id }}) === 1 ? 'currentColor' : 'none'" />
                    </button>
                    <button type="button" x-data @click="$store.ratings.rate({{ $content->id }}, -1)" :aria-pressed="$store.ratings.valueOf({{ $content->id }}) === -1" aria-label="Tidak suka"
                            class="flex size-11 items-center justify-center rounded-full border border-white/20 transition hover:bg-white/10">
                        <x-icon name="dislike" x-bind:fill="$store.ratings.valueOf({{ $content->id }}) === -1 ? 'currentColor' : 'none'" />
                    </button>
                    @if ($content->imdbUrl())
                        <a href="{{ $content->imdbUrl() }}" target="_blank" rel="noopener" class="text-sm font-semibold text-amber-400 hover:underline">IMDb</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="mt-8 space-y-10">
        @if ($content->isSeries())
            <section class="space-y-4 px-4 sm:px-6" x-data="{ activeSeason: {{ $content->seasons->first()?->id ?? 0 }} }" aria-labelledby="episodes-heading">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 id="episodes-heading" class="text-xl font-semibold">Episode</h2>
                    @if ($content->seasons->count() > 1)
                        <select x-model.number="activeSeason" aria-label="Pilih musim" class="rounded-full border border-white/15 bg-card px-4 py-2 text-sm">
                            @foreach ($content->seasons as $season)
                                <option value="{{ $season->id }}">{{ $season->displayTitle() }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                @forelse ($content->seasons as $season)
                    <ol x-show="activeSeason === {{ $season->id }}" class="space-y-2">
                        @forelse ($season->episodes as $episode)
                            <li x-reveal>
                                <a href="{{ route('watch', [$content, $episode]) }}" class="group flex items-center gap-4 rounded-2xl bg-card p-3 transition hover:bg-card-hover">
                                    <span class="w-6 shrink-0 text-center text-lg font-semibold text-muted">{{ $episode->episode_number }}</span>
                                    <span class="relative aspect-video w-28 shrink-0 overflow-hidden rounded-lg bg-ink sm:w-40">
                                        @if ($episode->thumbnailUrl() ?? $content->backdropUrl())
                                            <img src="{{ $episode->thumbnailUrl() ?? $content->backdropUrl() }}" alt="" loading="lazy" class="size-full object-cover object-top">
                                        @endif
                                        <span class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition group-hover:opacity-100"><x-icon name="play" /></span>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate font-medium">{{ $episode->title }}</span>
                                        @if ($episode->duration_min)
                                            <span class="text-xs text-muted">{{ $episode->duration_min }} menit</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                        @empty
                            <li class="py-6 text-center text-muted">Belum ada episode.</li>
                        @endforelse
                    </ol>
                @empty
                    <p class="py-6 text-center text-muted">Belum ada musim untuk serial ini.</p>
                @endforelse
            </section>
        @endif

        @if ($similar->isNotEmpty())
            <x-content-row title="Mirip dengan ini">
                @foreach ($similar as $similarContent)
                    <x-poster-card :content="$similarContent" />
                @endforeach
            </x-content-row>
        @endif
    </div>
</x-app-layout>
