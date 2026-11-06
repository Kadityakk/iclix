<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => 'Admin'])
    </head>
    <body x-data class="min-h-screen font-sans antialiased">
        <x-admin.sidebar />

        <main class="px-4 pt-6 pb-24 md:ml-60 md:px-8 md:pb-10">
            {{ $slot }}
        </main>

        <x-toast />
    </body>
</html>
