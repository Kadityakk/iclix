@php
$faqs = [
    'Apa itu iClix?' => 'Layanan streaming film dan serial yang bisa ditonton dari browser di perangkat apa pun.',
    'Bisakah dipakai bersama keluarga?' => 'Bisa. Satu akun punya sampai 4 profil, termasuk profil anak.',
    'Apakah pembayarannya sungguhan?' => 'Belum. Checkout di versi ini masih simulasi.',
];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body x-data class="font-sans antialiased">
        <header class="mx-auto flex max-w-6xl items-center justify-between px-5 py-5">
            <x-wordmark />
            <nav class="flex items-center gap-2" aria-label="Akun">
                <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-sm font-medium transition hover:bg-white/10">Masuk</a>
                <a href="{{ route('register') }}" class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-black transition hover:bg-white/85">Daftar</a>
            </nav>
        </header>

        <main class="mx-auto max-w-6xl space-y-24 px-5 pt-12 pb-24 md:pt-20">
            <section class="max-w-2xl space-y-6">
                <h1 class="text-4xl leading-tight font-bold tracking-tight md:text-6xl">Film dan serial favorit, kapan saja.</h1>
                <p class="text-lg text-muted">Rating dan poster langsung dari IMDb.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 font-semibold text-black transition hover:bg-white/85">
                        <x-icon name="play" class="size-4" /> Mulai sekarang
                    </a>
                    <a href="#paket" class="rounded-full border border-white/20 px-6 py-3 font-semibold transition hover:bg-white/10">Lihat paket</a>
                </div>
            </section>

            @if ($posters->isNotEmpty())
                <section class="space-y-4" aria-labelledby="popular-heading">
                    <h2 id="popular-heading" class="text-lg font-semibold">Populer</h2>
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 md:grid-cols-6">
                        @foreach ($posters as $poster)
                            <figure x-reveal style="transition-delay: {{ $loop->index * 60 }}ms" class="space-y-2">
                                <img src="{{ $poster->posterUrl() }}" alt="Poster {{ $poster->title }}" loading="lazy" decoding="async" class="aspect-[2/3] w-full rounded-xl bg-card object-cover">
                                <figcaption class="flex items-start justify-between gap-2 text-sm">
                                    <span class="truncate">{{ $poster->title }}</span>
                                    <x-imdb-rating :content="$poster" class="shrink-0 text-xs" />
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                </section>
            @endif

            <section id="paket" class="scroll-mt-10 space-y-8" aria-labelledby="plans-heading">
                <h2 id="plans-heading" x-reveal class="text-3xl font-bold">Pilih paket</h2>
                @include('plans.partials.grid', ['plans' => $plans, 'actionRoute' => 'register'])
            </section>

            <section class="max-w-3xl space-y-6" aria-labelledby="faq-heading">
                <h2 id="faq-heading" x-reveal class="text-3xl font-bold">Pertanyaan umum</h2>
                <div class="divide-y divide-white/10" x-data="{ opened: null }">
                    @foreach ($faqs as $question => $answer)
                        <div>
                            <button type="button" class="flex w-full items-center justify-between gap-4 py-4 text-left font-medium"
                                    @click="opened = opened === {{ $loop->index }} ? null : {{ $loop->index }}" :aria-expanded="opened === {{ $loop->index }}">
                                {{ $question }}
                                <x-icon name="plus" class="shrink-0 transition" x-bind:class="opened === {{ $loop->index }} && 'rotate-45'" />
                            </button>
                            <p x-show="opened === {{ $loop->index }}" x-cloak x-transition.opacity class="pb-4 text-sm text-muted">{{ $answer }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10 px-5 py-8 text-center text-sm text-muted">
            <p>iClix · Proyek kuliah · Data film dari IMDb</p>
        </footer>
    </body>
</html>
