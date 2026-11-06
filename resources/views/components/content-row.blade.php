@props(['title', 'id' => null])

<section @if ($id) id="{{ $id }}" @endif x-data="contentRow()" x-reveal class="relative space-y-3 px-4 sm:px-6" aria-label="{{ $title }}">
    <h2 class="text-lg font-semibold sm:text-xl">{{ $title }}</h2>

    <div class="group/row relative">
        <div x-ref="track" @scroll.passive.debounce.100ms="updateArrows()"
             class="scrollbar-none -my-4 flex snap-x gap-3 overflow-x-auto py-4 sm:gap-4">
            {{ $slot }}
        </div>

        <button type="button" x-show="canScrollLeft" x-cloak @click="scrollRow(-1)" aria-label="Geser ke kiri"
                class="absolute top-1/2 left-0 z-10 hidden size-11 -translate-y-1/2 items-center justify-center rounded-full bg-black/70 opacity-0 transition group-hover/row:opacity-100 focus-visible:opacity-100 md:flex">
            <x-icon name="left" />
        </button>
        <button type="button" x-show="canScrollRight" x-cloak @click="scrollRow(1)" aria-label="Geser ke kanan"
                class="absolute top-1/2 right-0 z-10 hidden size-11 -translate-y-1/2 items-center justify-center rounded-full bg-black/70 opacity-0 transition group-hover/row:opacity-100 focus-visible:opacity-100 md:flex">
            <x-icon name="right" />
        </button>
    </div>
</section>
