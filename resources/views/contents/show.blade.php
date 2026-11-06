<x-app-layout :title="$content->title">
    {{-- The backdrop lags behind the page while the title block drifts ahead of it. --}}
    <section data-parallax-root x-data x-init="$store.ratings.seed([{{ $content->id }}], {{ (int) $rating }})"
             class="relative isolate flex min-h-[75vh] items-end overflow-hidden bg-navy md:m-4 md:rounded-3xl">
        @if ($content->backdropUrl())
            <div x-parallax="{ scroll: 0.3, pointer: 14, origin: 'top' }" class="absolute -inset-x-8 -top-10 -bottom-32 -z-20">
                <img src="{{ $content->backdropUrl() }}" alt="" class="size-full object-cover">
            </div>
        @endif
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-navy via-navy/70 to-transparent md:bg-gradient-to-r md:via-navy/75"></div>

        <div x-parallax="{ scroll: -0.08, origin: 'top', fade: true }" class="max-w-3xl space-y-4 px-5 pt-28 pb-10 md:px-12">
            <p class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-white/80">
                <span>{{ $content->type->label() }}</span>
                <span>{{ $content->release_year }}</span>
                @if ($content->formattedDuration())
                    <span>{{ $content->formattedDuration() }}</span>
                @endif
                <span class="rounded border border-white/40 px-1.5 text-xs">{{ $content->age_rating->value }}</span>
                <span>{{ $content->genres->pluck('name')->join(', ') }}</span>
            </p>

            <h1 class="text-4xl leading-none font-bold tracking-tight uppercase md:text-6xl">{{ $content->title }}</h1>

            @if ($content->tagline)
                <p class="text-lg text-white/85">{{ $content->tagline }}</p>
            @endif

            <p class="leading-relaxed text-white/85">{{ $content->synopsis }}</p>

            @if ($content->cast_members)
                <p class="text-sm text-white/70">Pemeran: <span class="text-white/90">{{ implode(', ', $content->cast_members) }}</span></p>
            @endif

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <a href="{{ route('watch', $content) }}" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-black transition hover:bg-white/85">
                    <x-icon name="play" class="size-4" /> Tonton sekarang
                </a>
                @if ($content->trailerEmbedUrl())
                    <button type="button" x-data @click="$store.detail.openTrailer(@js($content->trailerEmbedUrl()))" class="rounded-full border border-white/60 px-6 py-3 text-sm font-semibold transition hover:bg-white/10">Trailer</button>
                @endif
                <button type="button" x-data @click="$store.myList.toggle({{ $content->id }})" :aria-pressed="$store.myList.has({{ $content->id }})" aria-label="Simpan ke Daftar Saya"
                        class="flex size-12 items-center justify-center rounded-full border border-white/30 bg-black/30 backdrop-blur transition hover:bg-white/20">
                    <x-icon name="bookmark" x-bind:fill="$store.myList.has({{ $content->id }}) ? 'currentColor' : 'none'" />
                </button>
                <button type="button" x-data @click="$store.ratings.rate({{ $content->id }}, 1)" :aria-pressed="$store.ratings.valueOf({{ $content->id }}) === 1" aria-label="Suka"
                        class="flex size-12 items-center justify-center rounded-full border border-white/30 bg-black/30 backdrop-blur transition hover:bg-white/20">
                    <x-icon name="like" x-bind:fill="$store.ratings.valueOf({{ $content->id }}) === 1 ? 'currentColor' : 'none'" />
                </button>
                <button type="button" x-data @click="$store.ratings.rate({{ $content->id }}, -1)" :aria-pressed="$store.ratings.valueOf({{ $content->id }}) === -1" aria-label="Tidak suka"
                        class="flex size-12 items-center justify-center rounded-full border border-white/30 bg-black/30 backdrop-blur transition hover:bg-white/20">
                    <x-icon name="dislike" x-bind:fill="$store.ratings.valueOf({{ $content->id }}) === -1 ? 'currentColor' : 'none'" />
                </button>
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
                                    <span class="relative aspect-video w-28 shrink-0 overflow-hidden rounded-lg bg-navy sm:w-40">
                                        @if ($episode->thumbnailUrl() ?? $content->backdropUrl())
                                            <img src="{{ $episode->thumbnailUrl() ?? $content->backdropUrl() }}" alt="" loading="lazy" class="size-full object-cover">
                                        @endif
                                        <span class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition group-hover:opacity-100"><x-icon name="play" /></span>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate font-medium">{{ $episode->title }}</span>
                                        @if ($episode->duration_min)
                                            <span class="text-xs text-muted">{{ $episode->duration_min }} menit</span>
                                        @endif
                                        <span class="mt-1 line-clamp-2 hidden text-sm text-muted sm:block">{{ $episode->synopsis }}</span>
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
