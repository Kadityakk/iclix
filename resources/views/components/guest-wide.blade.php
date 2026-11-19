@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => $title])
    </head>
    <body x-data class="min-h-screen font-sans antialiased">
        <div class="min-h-screen px-5 py-10">

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
