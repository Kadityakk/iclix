<x-app-layout title="Pencarian">
    <div class="space-y-6 px-4 pt-24 sm:px-6"
         x-data="liveSearch(@js($term), @js($results->map->toCardData()->values()))">
        <h1 class="text-3xl font-bold">Pencarian</h1>

        <div class="flex h-12 max-w-2xl items-center gap-3 rounded-full border border-white/15 bg-white/10 px-5 focus-within:border-white/40">
            <x-icon name="search" class="shrink-0 text-white/70" />
            <input type="search" x-model="query" @input.debounce.300ms="search(true)" placeholder="Cari judul film atau serial..." aria-label="Cari judul" autofocus
                   class="w-full border-0 bg-transparent p-0 text-white placeholder:text-white/60 focus:ring-0 focus:outline-none">
        </div>

        <div x-show="isLoading && results.length === 0" x-cloak class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6" aria-label="Memuat hasil">
            @foreach (range(1, 6) as $skeleton)
                <div class="aspect-[2/3] animate-pulse rounded-xl bg-white/10"></div>
            @endforeach
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            <template x-for="result in results" :key="result.id">
                <article class="group/card">
                    <a :href="result.url" class="block aspect-[2/3] overflow-hidden rounded-xl bg-card transition duration-300 group-hover/card:scale-105">
                        <img :src="result.poster" :alt="`Poster ${result.title}`" loading="lazy" class="size-full object-cover">
                    </a>
                    <h2 class="mt-2 truncate text-sm font-medium" x-text="result.title"></h2>
                    <p class="text-xs text-muted" x-text="`${result.type} · ${result.year}`"></p>
                </article>
            </template>
        </div>

        <p x-show="!isLoading && hasSearched && results.length === 0" x-cloak class="py-16 text-center text-muted">
            Tidak ada hasil untuk "<span x-text="query"></span>".
        </p>
        <p x-show="!isLoading && !hasSearched" class="py-16 text-center text-muted">Ketik judul untuk mulai mencari.</p>
    </div>
</x-app-layout>
