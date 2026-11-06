@props(['title', 'createUrl' => null, 'createLabel' => 'Tambah', 'backUrl' => null])

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        @if ($backUrl)
            <a href="{{ $backUrl }}" class="flex size-10 items-center justify-center rounded-full bg-white/10 transition hover:bg-white/20" aria-label="Kembali"><x-icon name="left" /></a>
        @endif
        <h1 class="text-2xl font-bold">{{ $title }}</h1>
    </div>

    @if ($createUrl)
        <a href="{{ $createUrl }}" class="inline-flex items-center gap-2 rounded-full bg-brand px-5 py-2.5 text-sm font-semibold transition hover:bg-brand-hover">
            <x-icon name="plus" class="size-4" /> {{ $createLabel }}
        </a>
    @endif
</div>
