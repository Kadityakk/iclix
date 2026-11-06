@php
$links = [
    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'chart'],
    ['label' => 'Konten', 'route' => 'admin.contents.index', 'active' => 'admin.contents.*', 'icon' => 'film'],
    ['label' => 'Genre', 'route' => 'admin.genres.index', 'active' => 'admin.genres.*', 'icon' => 'tag'],
    ['label' => 'Paket', 'route' => 'admin.plans.index', 'active' => 'admin.plans.*', 'icon' => 'card'],
    ['label' => 'Pengguna', 'route' => 'admin.users.index', 'active' => 'admin.users.*', 'icon' => 'users'],
];
@endphp

<aside class="fixed inset-y-0 left-0 z-40 hidden w-60 flex-col border-r border-white/10 bg-card p-5 md:flex">
    <a href="{{ route('admin.dashboard') }}" class="mb-8 block"><x-wordmark /><span class="ml-2 rounded bg-brand/30 px-1.5 py-0.5 text-xs font-medium">Admin</span></a>

    <nav class="flex-1 space-y-1" aria-label="Navigasi admin">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}"
               @if (request()->routeIs($link['active'])) aria-current="page" @endif
               @class([
                   'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                   'bg-white/15 text-white' => request()->routeIs($link['active']),
                   'text-muted hover:bg-white/10 hover:text-white' => ! request()->routeIs($link['active']),
               ])>
                <x-icon :name="$link['icon']" /> {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-muted transition hover:bg-white/10 hover:text-white">
            <x-icon name="logout" /> Keluar
        </button>
    </form>
</aside>

<nav class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-5 border-t border-white/10 bg-card md:hidden" aria-label="Navigasi admin">
    @foreach ($links as $link)
        <a href="{{ route($link['route']) }}" @class(['flex flex-col items-center gap-1 py-2.5 text-[11px] font-medium', 'text-white' => request()->routeIs($link['active']), 'text-muted' => ! request()->routeIs($link['active'])])>
            <x-icon :name="$link['icon']" /> {{ $link['label'] }}
        </a>
    @endforeach
</nav>
