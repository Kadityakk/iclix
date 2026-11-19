<div x-data x-show="$store.detail.isOpen" x-cloak
     x-transition.opacity
     @keydown.escape.window="$store.detail.close()"
     class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0" @click="$store.detail.close()"></div>

    {{-- Trailer: the iframe is removed on close, which also stops the audio. --}}
    <template x-if="$store.detail.trailerUrl">
        <div class="relative aspect-video w-full max-w-4xl overflow-hidden rounded-2xl bg-black">
            <iframe :src="`${$store.detail.trailerUrl}?autoplay=1&rel=0`" title="Trailer" class="size-full"
                    allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
            <button type="button" @click="$store.detail.close()" class="absolute top-3 right-3 flex size-10 items-center justify-center rounded-full bg-black/60 hover:bg-black/80" aria-label="Tutup trailer">
                <x-icon name="close" />
            </button>
        </div>
    </template>

    <template x-if="$store.detail.content">
        <article class="relative flex max-h-[90vh] w-full max-w-2xl gap-5 overflow-y-auto rounded-3xl bg-card p-5 sm:gap-6 sm:p-6">
            <img :src="$store.detail.content.poster" :alt="`Poster ${$store.detail.content.title}`" class="aspect-[2/3] w-28 shrink-0 self-start rounded-xl object-cover sm:w-44">

            <div class="min-w-0 flex-1 space-y-3">
                <button type="button" @click="$store.detail.close()" class="float-right -mt-1 -mr-1 flex size-9 items-center justify-center rounded-full hover:bg-white/10" aria-label="Tutup detail">
                    <x-icon name="close" />
                </button>
                <h2 class="text-2xl font-bold sm:text-3xl" x-text="$store.detail.content.title"></h2>
                <p class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted">
                    <span x-show="$store.detail.content.rating" class="inline-flex items-center gap-1 font-semibold text-amber-400">
                        <x-icon name="star" class="size-4" /><span x-text="$store.detail.content.rating"></span>
                    </span>
                    <span x-text="$store.detail.content.year"></span>
                    <span x-text="$store.detail.content.duration" x-show="$store.detail.content.duration"></span>
                    <span class="rounded border border-white/25 px-1.5 text-xs" x-text="$store.detail.content.age_rating"></span>
                </p>
                <p class="text-sm leading-relaxed text-white/75" x-text="$store.detail.content.synopsis"></p>
                <div class="flex flex-wrap gap-3 pt-1">
                    <a :href="$store.detail.content.watch_url" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-black transition hover:bg-white/85">
                        <x-icon name="play" class="size-4" /> Tonton
                    </a>
                    <a :href="$store.detail.content.url" class="inline-flex items-center rounded-full border border-white/20 px-5 py-2.5 text-sm font-semibold transition hover:bg-white/10">
                        Detail
                    </a>
                </div>
            </div>
        </article>
    </template>
</div>
