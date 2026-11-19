@props(['content', 'fluid' => false])

<article @class(['group/card relative space-y-2', 'w-full' => $fluid, 'w-36 shrink-0 snap-start sm:w-44 lg:w-48' => ! $fluid])>
    <div class="relative aspect-[2/3] overflow-hidden rounded-xl bg-card transition duration-300 group-hover/card:-translate-y-1 group-focus-within/card:-translate-y-1">
        <a href="{{ route('contents.show', $content) }}" class="block size-full" aria-label="Detail {{ $content->title }}">
            @if ($content->posterUrl())
                <img src="{{ $content->posterUrl() }}" alt="Poster {{ $content->title }}" loading="lazy" decoding="async" class="size-full object-cover">
            @else
                <span class="flex size-full items-center justify-center p-3 text-center text-sm font-semibold">{{ $content->title }}</span>
            @endif
        </a>

        <div class="pointer-events-none absolute inset-0 flex items-end gap-2 bg-black/50 p-3 opacity-0 transition duration-300 group-hover/card:opacity-100 group-focus-within/card:opacity-100">
            <a href="{{ route('watch', $content) }}" class="pointer-events-auto flex size-9 items-center justify-center rounded-full bg-white text-black transition hover:bg-white/85" aria-label="Tonton {{ $content->title }}">
                <x-icon name="play" class="size-4" />
            </a>
            <button type="button" x-data @click="$store.myList.toggle({{ $content->id }})"
                    class="pointer-events-auto flex size-9 items-center justify-center rounded-full border border-white/40 bg-black/60 transition hover:bg-white/20"
                    :aria-label="$store.myList.has({{ $content->id }}) ? 'Hapus dari Daftar Saya' : 'Simpan ke Daftar Saya'">
                <x-icon name="bookmark" class="size-4" x-bind:fill="$store.myList.has({{ $content->id }}) ? 'currentColor' : 'none'" />
            </button>
        </div>
    </div>

    <div class="flex items-start justify-between gap-2 text-sm">
        <h3 class="truncate font-medium">{{ $content->title }}</h3>
        <x-imdb-rating :content="$content" class="shrink-0 text-xs" />
    </div>
</article>
