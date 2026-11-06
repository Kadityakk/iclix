<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => $title ?? null, 'watchlistIds' => $watchlistIds])
    </head>
    <body x-data class="min-h-screen font-sans antialiased">
        <x-sidebar />

        <div class="relative md:pl-24">
            <header class="absolute inset-x-0 top-0 z-30 flex items-center justify-between gap-3 px-4 py-4 sm:px-6 md:justify-end">
                <a href="{{ route('browse') }}" class="md:hidden" aria-label="Beranda iClix"><x-wordmark /></a>

                <div class="flex items-center gap-3">
                    <x-search-bar />

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex size-11 items-center justify-center rounded-full border border-white/15 bg-black/30 backdrop-blur-md transition hover:border-white/40" aria-label="Menu akun">
                                @if ($currentProfile)
                                    <x-avatar :profile="$currentProfile" class="size-9 text-sm" />
                                @else
                                    <x-icon name="user" />
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profiles.index')">Ganti profil</x-dropdown-link>
                            <x-dropdown-link :href="route('account.edit')">Akun &amp; langganan</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Keluar</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            <main class="pb-24 md:pb-10">
                {{ $slot }}
            </main>
        </div>

        <x-detail-modal />
        <x-toast />
    </body>
</html>
