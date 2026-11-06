<div x-data x-show="$store.detail.isOpen" x-cloak
     x-transition.opacity
     @keydown.escape.window="$store.detail.close()"
     class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0" @click="$store.detail.close()"></div>

    {{-- Trailer: the iframe is removed on close, which also stops the audio. --}}
    <template x-if="$store.detail.trailerUrl">
        <div class="relative aspect-video w-full max-w-4xl overflow-hidden rounded-2xl bg-black shadow-2xl">
            <iframe :src="`${$store.detail.trailerUrl}?autoplay=1&rel=0`" title="Trailer" class="size-full"
                    allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
            <button type="button" @click="$store.detail.close()" class="absolute top-3 right-3 flex size-10 items-center justify-center rounded-full bg-black/60 hover:bg-black/80" aria-label="Tutup trailer">
                <x-icon name="close" />
            </button>
        </div>
    </template>

    <template x-if="$store.detail.content">
        <article class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl bg-card shadow-2xl">
            <div class="relative aspect-video">
                <img :src="$store.detail.content.backdrop" :alt="`Gambar ${$store.detail.content.title}`" class="size-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-card via-card/10 to-transparent"></div>
                <button type="button" @click="$store.detail.close()" class="absolute top-3 right-3 flex size-10 items-center justify-center rounded-full bg-black/60 hover:bg-black/80" aria-label="Tutup detail">
                    <x-icon name="close" />
                </button>
            </div>
            <div class="space-y-4 p-6">
                <h2 class="text-3xl font-bold" x-text="$store.detail.content.title"></h2>
                <p class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted">
                    <span x-text="$store.detail.content.year"></span>
                    <span x-text="$store.detail.content.duration" x-show="$store.detail.content.duration"></span>
                    <span class="rounded border border-white/30 px-1.5 text-xs" x-text="$store.detail.content.age_rating"></span>
                    <span x-text="$store.detail.content.genres.join(', ')"></span>
                </p>
                <p class="text-sm leading-relaxed text-body/90" x-text="$store.detail.content.synopsis"></p>
                <p class="text-sm text-muted" x-show="$store.detail.content.cast.length > 0">
                    Pemeran: <span class="text-body/90" x-text="$store.detail.content.cast.join(', ')"></span>
                </p>
                <div class="flex flex-wrap gap-3 pt-1">
                    <a :href="$store.detail.content.watch_url" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-2.5 text-sm font-semibold text-black transition hover:bg-white/85">
                        <x-icon name="play" class="size-4" /> Tonton sekarang
                    </a>
                    <a :href="$store.detail.content.url" class="inline-flex items-center rounded-full border border-white/40 px-6 py-2.5 text-sm font-semibold transition hover:bg-white/10">
                        Lihat detail
                    </a>
                </div>
            </div>
        </article>
    </template>
</div>
