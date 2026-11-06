@props(['content', 'fluid' => false])

<article @class(['group/card relative', 'w-full' => $fluid, 'w-36 shrink-0 snap-start sm:w-44 lg:w-48' => ! $fluid])>
    <div class="relative aspect-[2/3] overflow-hidden rounded-xl bg-card shadow-lg transition duration-300 group-hover/card:scale-105 group-hover/card:shadow-2xl group-hover/card:shadow-brand/30 group-focus-within/card:scale-105">
        <a href="{{ route('contents.show', $content) }}" class="block size-full" aria-label="Detail {{ $content->title }}">
            @if ($content->posterUrl())
                <img src="{{ $content->posterUrl() }}" alt="Poster {{ $content->title }}" loading="lazy" decoding="async" class="size-full object-cover">
            @else
                <span class="flex size-full items-center justify-center bg-gradient-to-br from-navy to-brand/40 p-3 text-center text-sm font-semibold">{{ $content->title }}</span>
            @endif
        </a>

        <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-black via-black/80 to-transparent p-3 pt-12 opacity-0 transition duration-300 group-hover/card:opacity-100 group-focus-within/card:opacity-100">
            <h3 class="line-clamp-2 text-sm leading-snug font-semibold">{{ $content->title }}</h3>
            <p class="mt-1 text-xs text-white/70">{{ $content->type->label() }} · {{ $content->release_year }} · {{ $content->age_rating->value }}</p>
            <div class="pointer-events-auto mt-2 flex gap-2">
                <a href="{{ route('watch', $content) }}" class="flex size-9 items-center justify-center rounded-full bg-white text-black transition hover:bg-white/85" aria-label="Tonton {{ $content->title }}">
                    <x-icon name="play" class="size-4" />
                </a>
                <button type="button" x-data @click="$store.myList.toggle({{ $content->id }})"
                        class="flex size-9 items-center justify-center rounded-full border border-white/40 bg-black/40 transition hover:bg-white/20"
                        :aria-label="$store.myList.has({{ $content->id }}) ? 'Hapus dari Daftar Saya' : 'Simpan ke Daftar Saya'">
                    <x-icon name="bookmark" class="size-4" x-bind:fill="$store.myList.has({{ $content->id }}) ? 'currentColor' : 'none'" />
                </button>
            </div>
        </div>
    </div>
</article>
