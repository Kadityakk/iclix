@props(['items', 'likedContentIds'])

<section x-data="heroSlider(@js($items))"
         x-init="$store.ratings.seed(@js($likedContentIds), 1)"
         @mouseenter="isPaused = true" @mouseleave="isPaused = false"
         @keydown.left.window="if (! $event.target.closest('input, textarea, select')) goTo(active - 1)"
         @keydown.right.window="if (! $event.target.closest('input, textarea, select')) goTo(active + 1)"
         class="bg-card px-5 pt-24 pb-8 md:m-4 md:rounded-3xl md:px-12 md:pt-20 md:pb-12"
         aria-roledescription="carousel" aria-label="Konten unggulan">
    <div class="mx-auto flex max-w-6xl flex-col-reverse gap-8 md:flex-row md:items-center md:justify-between">
        <div class="max-w-xl space-y-5 transition-opacity duration-300" :class="isTextVisible ? 'opacity-100' : 'opacity-0'">
            <p class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted">
                <span x-show="current.rating" class="inline-flex items-center gap-1 font-semibold text-amber-400">
                    <x-icon name="star" class="size-4" /><span x-text="current.rating"></span>
                </span>
                <span x-text="current.year"></span>
                <span x-show="current.duration" x-text="current.duration"></span>
                <span class="rounded border border-white/25 px-1.5 text-xs" x-text="current.age_rating"></span>
            </p>

            <h1 class="text-4xl leading-tight font-bold tracking-tight md:text-5xl lg:text-6xl" x-text="current.title"></h1>

            <p class="line-clamp-2 text-white/70" x-text="current.synopsis"></p>

            <div class="flex flex-wrap items-center gap-3">
                <a :href="current.watch_url" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-black transition hover:bg-white/85">
                    <x-icon name="play" class="size-4" /> Tonton
                </a>
                <button type="button" @click="$store.detail.open(current)" aria-label="Info detail"
                        class="flex size-11 items-center justify-center rounded-full border border-white/20 transition hover:bg-white/10">
                    <x-icon name="info" />
                </button>
                <button type="button" @click="$store.myList.toggle(current.id)" aria-label="Simpan ke Daftar Saya" :aria-pressed="$store.myList.has(current.id)"
                        class="flex size-11 items-center justify-center rounded-full border border-white/20 transition hover:bg-white/10">
                    <x-icon name="bookmark" x-bind:fill="$store.myList.has(current.id) ? 'currentColor' : 'none'" />
                </button>
                <button type="button" @click="$store.ratings.rate(current.id, 1)" aria-label="Suka" :aria-pressed="$store.ratings.valueOf(current.id) === 1"
                        class="flex size-11 items-center justify-center rounded-full border border-white/20 transition hover:bg-white/10">
                    <x-icon name="heart" x-bind:fill="$store.ratings.valueOf(current.id) === 1 ? 'currentColor' : 'none'" />
                </button>
            </div>

            <div class="flex gap-2 pt-2" x-show="items.length > 1">
                <template x-for="(item, index) in items" :key="item.id">
                    <button type="button" @click="goTo(index)" :aria-label="`Tampilkan ${item.title}`" :aria-current="index === active"
                            class="h-1.5 rounded-full transition-all"
                            :class="index === active ? 'w-8 bg-white' : 'w-4 bg-white/25 hover:bg-white/50'"></button>
                </template>
            </div>
        </div>

        <div class="relative mx-auto aspect-[2/3] w-48 shrink-0 overflow-hidden rounded-2xl bg-ink sm:w-56 md:mx-0 lg:w-72">
            <template x-for="(item, index) in items" :key="item.id">
                <img :src="item.poster" :alt="index === active ? `Poster ${item.title}` : ''"
                     :loading="index === 0 ? 'eager' : 'lazy'"
                     class="absolute inset-0 size-full object-cover transition-opacity duration-500"
                     :class="index === active ? 'opacity-100' : 'opacity-0'">
            </template>
        </div>
    </div>
</section>
