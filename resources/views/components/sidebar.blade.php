@php
$links = [
    ['label' => 'Beranda', 'route' => 'browse', 'icon' => 'home'],
    ['label' => 'Film', 'route' => 'movies.index', 'icon' => 'film'],
    ['label' => 'Serial', 'route' => 'series.index', 'icon' => 'tv'],
    ['label' => 'Trending', 'route' => 'trending.index', 'icon' => 'flame'],
    ['label' => 'Daftar Saya', 'route' => 'my-list.index', 'icon' => 'bookmark'],
    ['label' => 'Genre', 'route' => 'genres.index', 'icon' => 'tag'],
];
$bottomLinks = array_values(array_filter($links, fn (array $link) => ! in_array($link['route'], ['trending.index', 'genres.index'], true)));
$bottomLinks[] = ['label' => 'Akun', 'route' => 'account.edit', 'icon' => 'user'];
@endphp

<aside class="fixed inset-y-0 left-0 z-40 hidden w-24 flex-col items-center gap-4 border-r border-white/10 bg-ink py-6 md:flex">
    <a href="{{ route('browse') }}" class="mb-6 rounded-lg" aria-label="Beranda iClix">
        <x-application-logo class="size-9" />
    </a>

    <nav class="flex flex-col items-center gap-4" aria-label="Navigasi utama">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}" title="{{ $link['label'] }}" aria-label="{{ $link['label'] }}"
               @if (request()->routeIs($link['route'])) aria-current="page" @endif
               @class([
                   'flex size-12 items-center justify-center rounded-full transition',
                   'bg-white/15 text-white' => request()->routeIs($link['route']),
                   'bg-white/5 text-muted hover:bg-white/15 hover:text-white' => ! request()->routeIs($link['route']),
               ])>
                <x-icon :name="$link['icon']" />
            </a>
        @endforeach
    </nav>
</aside>

<nav class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-5 border-t border-white/10 bg-ink/95 backdrop-blur-md md:hidden" aria-label="Navigasi bawah">
    @foreach ($bottomLinks as $link)
        <a href="{{ route($link['route']) }}"
           @if (request()->routeIs($link['route'])) aria-current="page" @endif
           @class([
               'flex flex-col items-center gap-1 py-2.5 text-[11px] font-medium',
               'text-white' => request()->routeIs($link['route']),
               'text-muted' => ! request()->routeIs($link['route']),
           ])>
            <x-icon :name="$link['icon']" />
            {{ $link['label'] }}
        </a>
    @endforeach
</nav>
