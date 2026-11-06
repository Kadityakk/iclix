<a href="{{ route('search') }}" class="flex size-11 items-center justify-center rounded-full border border-white/15 bg-black/30 backdrop-blur-md sm:hidden" aria-label="Cari">
    <x-icon name="search" />
</a>

<div class="relative hidden sm:block" x-data="liveSearch()" @click.outside="isOpen = false" @keydown.escape="isOpen = false">
    <form action="{{ route('search') }}" method="GET" role="search"
          class="flex h-11 w-72 items-center gap-2 rounded-full border border-white/10 bg-white/10 pr-1.5 pl-4 backdrop-blur-md transition focus-within:border-white/40 lg:w-80">
        <x-icon name="search" class="shrink-0 text-white/70" />
        <input type="search" name="q" placeholder="Cari film..." aria-label="Cari film" autocomplete="off"
               x-model="query" @input.debounce.300ms="search(); isOpen = true" @focus="isOpen = query.trim() !== ''"
               class="w-full border-0 bg-transparent p-0 text-sm text-white placeholder:text-white/60 focus:ring-0 focus:outline-none">
        <a href="{{ route('movies.index') }}" class="flex size-8 shrink-0 items-center justify-center rounded-full bg-white/15 transition hover:bg-white/25" aria-label="Filter katalog" title="Filter katalog">
            <x-icon name="sliders" class="size-4" />
        </a>
    </form>

    <div x-show="isOpen && query.trim() !== ''" x-cloak x-transition.opacity
         class="absolute right-0 mt-2 max-h-96 w-full overflow-y-auto rounded-2xl border border-white/10 bg-card p-2 shadow-2xl">
        <template x-if="isLoading && results.length === 0">
            <div class="space-y-2 p-1" aria-label="Memuat hasil">
                <div class="h-14 animate-pulse rounded-lg bg-white/10"></div>
                <div class="h-14 animate-pulse rounded-lg bg-white/10"></div>
            </div>
        </template>

        <template x-for="result in results.slice(0, 6)" :key="result.id">
            <a :href="result.url" class="flex items-center gap-3 rounded-lg p-1.5 transition hover:bg-white/10">
                <img :src="result.poster" :alt="`Poster ${result.title}`" class="h-14 w-10 rounded object-cover" loading="lazy">
                <span class="min-w-0">
                    <span class="block truncate text-sm font-medium" x-text="result.title"></span>
                    <span class="block text-xs text-muted" x-text="`${result.type} · ${result.year}`"></span>
                </span>
            </a>
        </template>

        <p x-show="!isLoading && hasSearched && results.length === 0" class="p-3 text-center text-sm text-muted">Judul tidak ditemukan.</p>
    </div>
</div>
