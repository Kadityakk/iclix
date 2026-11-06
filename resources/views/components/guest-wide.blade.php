@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => $title])
    </head>
    <body x-data class="min-h-screen font-sans antialiased">
        <div data-parallax-root class="relative isolate min-h-screen overflow-hidden px-5 py-10">
            <div class="absolute inset-0 -z-20 bg-gradient-to-br from-navy via-ink to-ink"></div>
            <div x-parallax="{ scroll: 0, pointer: 40 }" class="absolute -top-32 -left-24 -z-10 size-[28rem] rounded-full bg-glow/20 blur-3xl"></div>
            <div x-parallax="{ scroll: 0, pointer: -60 }" class="absolute -right-24 -bottom-32 -z-10 size-[32rem] rounded-full bg-brand/20 blur-3xl"></div>

            <header class="mb-10 flex items-center justify-between">
                <x-wordmark />
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-full px-4 py-2 text-sm font-medium transition hover:bg-white/10">Keluar</button>
                </form>
            </header>

            <main>{{ $slot }}</main>
        </div>

        <x-toast />
    </body>
</html>
