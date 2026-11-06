@props(['items', 'likedContentIds'])

{{-- Parallax layers, back to front: backdrop, glow, then the text block. Each one moves at its own speed. --}}
<section x-data="heroSlider(@js($items))" data-parallax-root
         x-init="$store.ratings.seed(@js($likedContentIds), 1)"
         @mouseenter="isPaused = true" @mouseleave="isPaused = false"
         @keydown.left.window="if (! $event.target.closest('input, textarea, select')) goTo(active - 1)"
         @keydown.right.window="if (! $event.target.closest('input, textarea, select')) goTo(active + 1)"
         class="relative isolate flex min-h-[calc(100svh-4.5rem)] flex-col justify-end overflow-hidden bg-navy md:m-4 md:min-h-[85vh] md:justify-center md:rounded-3xl"
         aria-roledescription="carousel" aria-label="Konten unggulan">
    <div x-parallax="{ scroll: 0.28, pointer: 16, origin: 'top' }" class="absolute -inset-x-8 -top-12 -bottom-40 -z-30">
        <template x-for="(item, index) in items" :key="item.id">
            <img :src="item.backdrop" :alt="index === active ? `Gambar ${item.title}` : ''"
                 :loading="index === 0 ? 'eager' : 'lazy'"
                 class="absolute inset-0 size-full object-cover transition-opacity duration-700"
                 :class="index === active ? 'opacity-100' : 'opacity-0'">
        </template>
    </div>

    <div class="absolute inset-0 -z-20 bg-gradient-to-t from-navy via-navy/60 to-transparent md:bg-gradient-to-r md:from-navy md:via-navy/80 md:to-transparent"></div>
    <div x-parallax="{ scroll: -0.12, pointer: -32, origin: 'top' }" class="absolute -top-20 -left-20 -z-10 size-[26rem] rounded-full bg-fuchsia-600/30 blur-3xl"></div>
    <div x-parallax="{ scroll: 0.1, pointer: 24, origin: 'top' }" class="absolute right-1/3 -bottom-24 -z-10 size-72 rounded-full bg-brand/20 blur-3xl"></div>

    <div x-parallax="{ scroll: -0.08, pointer: -6, origin: 'top', fade: true }" class="relative max-w-3xl space-y-5 px-5 pt-24 pb-6 md:px-14 md:py-20">
        <div class="space-y-5 transition-opacity duration-300" :class="isTextVisible ? 'opacity-100' : 'opacity-0'">
            <p class="flex flex-wrap gap-x-12 gap-y-1 text-base text-white/80 lg:text-lg">
                <span x-text="current.date ?? current.year"></span>
                <span x-text="(current.genres ?? []).join(', ').toLowerCase()"></span>
            </p>

            <div class="space-y-3">
                <h1 class="line-clamp-3 text-4xl leading-none font-bold tracking-tight uppercase md:line-clamp-2" :class="(current.title ?? '').length > 16 ? 'md:text-5xl lg:text-6xl' : 'md:text-6xl lg:text-8xl'" x-text="current.title"></h1>
                <p class="text-lg text-white/85 md:text-2xl">
                    <span x-text="current.tagline"></span>
                    <span class="text-white/60" x-text="`(${current.year})`"></span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a :href="current.watch_url" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-black transition hover:bg-white/85">
                    <x-icon name="play" class="size-4" /> Tonton sekarang
                </a>
                <button type="button" x-show="current.trailer" @click="$store.detail.openTrailer(current.trailer)"
                        class="rounded-full border border-white/60 px-6 py-3 text-sm font-semibold transition hover:bg-white/10">
                    Trailer
                </button>
            </div>
        </div>

        <div class="scrollbar-none flex gap-3 overflow-x-auto pt-2">
            <template x-for="(item, index) in items" :key="item.id">
                <button type="button" @click="goTo(index)" :aria-label="`Tampilkan ${item.title}`" :aria-current="index === active"
                        class="size-16 shrink-0 overflow-hidden rounded-xl transition md:size-20"
                        :class="[index === active ? 'ring-2 ring-white' : 'opacity-60 hover:opacity-90', index === 4 ? 'md:hidden lg:block' : '']">
                    <img :src="item.poster" alt="" loading="lazy" class="size-full object-cover">
                </button>
            </template>
        </div>

        <div class="h-0.5 max-w-sm overflow-hidden rounded-full bg-white/20" aria-hidden="true">
            <div class="h-full bg-white" :style="`width: ${progressPercent}%`"></div>
        </div>
    </div>

    <div class="flex gap-3 px-5 pb-6 md:absolute md:right-8 md:bottom-8 md:p-0">
        <button type="button" @click="$store.ratings.rate(current.id, 1)" aria-label="Suka" :aria-pressed="$store.ratings.valueOf(current.id) === 1"
                class="flex size-12 items-center justify-center rounded-full border border-white/30 bg-black/30 backdrop-blur transition hover:bg-white/20">
            <x-icon name="heart" x-bind:fill="$store.ratings.valueOf(current.id) === 1 ? 'currentColor' : 'none'" />
        </button>
        <button type="button" @click="$store.myList.toggle(current.id)" aria-label="Simpan ke Daftar Saya" :aria-pressed="$store.myList.has(current.id)"
                class="flex size-12 items-center justify-center rounded-full border border-white/30 bg-black/30 backdrop-blur transition hover:bg-white/20">
            <x-icon name="bookmark" x-bind:fill="$store.myList.has(current.id) ? 'currentColor' : 'none'" />
        </button>
        <button type="button" @click="$store.detail.open(current)" aria-label="Info detail"
                class="flex size-12 items-center justify-center rounded-full border border-white/30 bg-black/30 backdrop-blur transition hover:bg-white/20">
            <x-icon name="info" />
        </button>
    </div>
</section>
