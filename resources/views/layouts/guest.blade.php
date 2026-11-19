<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body x-data class="min-h-screen font-sans antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">

            <a href="{{ route('home') }}" class="mb-6 rounded-lg" aria-label="iClix">
                <x-wordmark class="text-2xl" />
            </a>

            <main class="w-full max-w-md rounded-3xl bg-card p-6 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
