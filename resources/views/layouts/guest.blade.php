<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body x-data class="min-h-screen font-sans antialiased">
        <div data-parallax-root class="relative isolate flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-10">
            <div class="absolute inset-0 -z-20 bg-gradient-to-br from-navy via-ink to-ink"></div>
            <div x-parallax="{ scroll: 0, pointer: 40 }" class="absolute -top-32 -left-24 -z-10 size-[28rem] rounded-full bg-glow/25 blur-3xl"></div>
            <div x-parallax="{ scroll: 0, pointer: -60 }" class="absolute -right-24 -bottom-32 -z-10 size-[32rem] rounded-full bg-brand/25 blur-3xl"></div>

            <a href="{{ route('home') }}" class="mb-6 rounded-lg" aria-label="iClix">
                <x-wordmark class="text-2xl" />
            </a>

            <main class="w-full max-w-md rounded-3xl border border-white/10 bg-card/80 p-6 shadow-2xl backdrop-blur-xl sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
