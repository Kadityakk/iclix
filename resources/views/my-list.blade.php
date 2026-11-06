<x-app-layout title="Daftar Saya">
    <div class="space-y-6 px-4 pt-24 sm:px-6">
        <h1 class="text-3xl font-bold">Daftar Saya</h1>

        @if ($contents->isEmpty())
            <div class="py-16 text-center">
                <p class="text-muted">Belum ada judul yang disimpan.</p>
                <a href="{{ route('browse') }}" class="mt-4 inline-block rounded-full bg-white px-6 py-2.5 text-sm font-semibold text-black">Jelajahi katalog</a>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @foreach ($contents as $content)
                    <div x-data x-show="$store.myList.has({{ $content->id }})">
                        <x-poster-card :content="$content" fluid />
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
