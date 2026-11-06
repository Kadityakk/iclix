@php
mt_srand(11);
$stars = collect(range(1, 90))->map(fn () => ['x' => mt_rand(0, 1600), 'y' => mt_rand(0, 620), 'r' => mt_rand(4, 16) / 10, 'o' => mt_rand(25, 90) / 100]);
$posterLayout = [
    ['class' => 'top-[14%] right-[4%] w-44 lg:w-56 rotate-3', 'scroll' => 0.34, 'pointer' => 42],
    ['class' => 'top-[8%] right-[30%] w-32 lg:w-40 -rotate-6', 'scroll' => 0.12, 'pointer' => 18],
    ['class' => 'top-[48%] right-[24%] w-36 lg:w-48 rotate-2', 'scroll' => 0.46, 'pointer' => 58],
    ['class' => 'top-[56%] right-[2%] w-28 lg:w-36 -rotate-3', 'scroll' => 0.2, 'pointer' => 26],
    ['class' => 'top-[30%] right-[47%] w-24 lg:w-28 rotate-6', 'scroll' => 0.06, 'pointer' => 10],
];
$faqs = [
    'Apa itu iClix?' => 'iClix adalah layanan streaming film dan serial. Pilih paket, buat profil, lalu tonton dari browser di laptop, tablet, atau ponsel.',
    'Berapa biayanya?' => 'Tersedia paket Basic, Standar, dan Premium dengan masa aktif 30 hari. Harga dan kualitas gambar tiap paket bisa dilihat di bagian Paket.',
    'Apakah pembayarannya sungguhan?' => 'Belum. Versi ini memakai checkout simulasi: tombol Bayar langsung mengaktifkan langganan tanpa memungut biaya.',
    'Bisakah dipakai bersama keluarga?' => 'Bisa. Satu akun mendukung sampai 4 profil, termasuk profil anak yang hanya menampilkan konten SU dan 7+.',
    'Bisakah melanjutkan tontonan?' => 'Bisa. Posisi tonton tersimpan otomatis dan judulnya muncul di baris Lanjutkan Menonton pada profil Anda.',
];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body x-data class="font-sans antialiased">
        <header class="absolute inset-x-0 top-0 z-30 flex items-center justify-between px-5 py-5 md:px-12">
            <x-wordmark />
            <nav class="flex items-center gap-2 sm:gap-3" aria-label="Akun">
                <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-sm font-medium transition hover:bg-white/10">Masuk</a>
                <a href="{{ route('register') }}" class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-black transition hover:bg-white/85">Daftar</a>
            </nav>
        </header>

        <main>
            {{-- Parallax scene, back to front: stars, moon, far ridge, posters, mid ridge, text, near ridge. --}}
            <section data-parallax-root class="relative isolate flex min-h-svh items-center overflow-hidden bg-gradient-to-b from-[#07071a] via-navy to-[#3a1465]">
                <div x-parallax="{ scroll: 0.62, origin: 'top' }" class="absolute inset-x-0 top-0 -z-40 h-[75%]" aria-hidden="true">
                    <svg viewBox="0 0 1600 620" preserveAspectRatio="xMidYMid slice" class="size-full">
                        @foreach ($stars as $star)
                            <circle cx="{{ $star['x'] }}" cy="{{ $star['y'] }}" r="{{ $star['r'] }}" fill="#fff" opacity="{{ $star['o'] }}" />
                        @endforeach
                    </svg>
                </div>

                <div x-parallax="{ scroll: 0.5, pointer: 14, origin: 'top' }" class="absolute top-[12%] left-[46%] -z-30" aria-hidden="true">
                    <div class="size-64 rounded-full bg-gradient-to-br from-fuchsia-300 via-glow to-brand shadow-[0_0_140px_50px_rgba(192,38,211,0.45)] md:size-96"></div>
                </div>

                <div x-parallax="{ scroll: 0.38, pointer: 8, origin: 'top' }" class="absolute inset-x-[-6%] bottom-[-4%] -z-20 h-[58%]" aria-hidden="true">
                    <svg viewBox="0 0 1600 500" preserveAspectRatio="none" class="size-full">
                        <path fill="#2a1259" d="M0 330 170 210l130 80 190-180 170 150 120-90 210 170 190-210 170 130 150-70 120 80v230H0Z" />
                    </svg>
                </div>

                <div class="absolute inset-0 -z-10 hidden md:block" aria-hidden="true">
                    @foreach ($posters->take(count($posterLayout)) as $index => $poster)
                        <div x-parallax="{ scroll: {{ $posterLayout[$index]['scroll'] }}, pointer: {{ $posterLayout[$index]['pointer'] }}, origin: 'top' }"
                             class="absolute {{ $posterLayout[$index]['class'] }}">
                            <img src="{{ $poster->posterUrl() }}" alt="" decoding="async" class="animate-drift aspect-[2/3] w-full rounded-2xl object-cover shadow-2xl shadow-black/60 ring-1 ring-white/15" style="animation-delay: -{{ $index * 1.7 }}s">
                        </div>
                    @endforeach
                </div>

                <div x-parallax="{ scroll: 0.22, pointer: 5, origin: 'top' }" class="absolute inset-x-[-6%] bottom-[-3%] -z-10 h-[38%]" aria-hidden="true">
                    <svg viewBox="0 0 1600 400" preserveAspectRatio="none" class="size-full">
                        <path fill="#180b3a" d="M0 230 210 120l170 110 150-70 220 140 200-160 240 170 170-90 240 100v270H0Z" />
                    </svg>
                </div>

                <div x-parallax="{ scroll: -0.14, pointer: -8, origin: 'top', fade: true }" class="relative z-10 max-w-2xl px-5 pt-24 pb-44 md:pl-12 lg:pl-20">
                    <p class="mb-4 text-sm font-semibold tracking-[0.25em] text-fuchsia-300 uppercase">Film &amp; serial tanpa batas</p>
                    <h1 class="text-5xl leading-[0.95] font-extrabold tracking-tight uppercase md:text-7xl lg:text-8xl">Malam ini nonton apa?</h1>
                    <p class="mt-6 max-w-lg text-lg text-white/80">Jelajahi katalog iClix, simpan favorit Anda, dan lanjutkan tontonan di perangkat apa pun.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 font-semibold text-black transition hover:bg-white/85">
                            <x-icon name="play" class="size-4" /> Mulai sekarang
                        </a>
                        <a href="#paket" class="rounded-full border border-white/50 px-7 py-3.5 font-semibold transition hover:bg-white/10">Lihat paket</a>
                    </div>
                </div>

                <div class="absolute inset-x-0 bottom-0 z-0 h-40" aria-hidden="true">
                    <svg viewBox="0 0 1600 160" preserveAspectRatio="none" class="size-full">
                        <path fill="#0b0b0f" d="M0 90 240 30l210 70 260-60 300 80 270-70 320 60v50H0Z" />
                    </svg>
                </div>
            </section>

            <section class="mx-auto grid max-w-6xl gap-5 px-5 py-20 md:grid-cols-3" aria-label="Keunggulan">
                @foreach ([
                    ['icon' => 'play', 'title' => 'Lanjutkan kapan saja', 'text' => 'Posisi tonton tersimpan otomatis tiap 10 detik, jadi Anda selalu mulai dari adegan terakhir.'],
                    ['icon' => 'users', 'title' => 'Sampai 4 profil', 'text' => 'Setiap anggota keluarga punya daftar dan riwayat sendiri, termasuk profil khusus anak.'],
                    ['icon' => 'bookmark', 'title' => 'Daftar Saya', 'text' => 'Simpan judul yang menarik dengan satu klik, lalu tonton saat ada waktu.'],
                ] as $feature)
                    <article x-reveal style="transition-delay: {{ $loop->index * 120 }}ms" class="rounded-3xl border border-white/10 bg-card p-7">
                        <span class="mb-5 flex size-12 items-center justify-center rounded-2xl bg-brand/20 text-brand-hover"><x-icon :name="$feature['icon']" /></span>
                        <h2 class="text-xl font-semibold">{{ $feature['title'] }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $feature['text'] }}</p>
                    </article>
                @endforeach
            </section>

            <section id="paket" data-parallax-root class="relative isolate scroll-mt-10 overflow-hidden px-5 py-20">
                <div x-parallax="{ scroll: 0.3, pointer: 30 }" class="absolute top-0 -left-20 -z-10 size-96 rounded-full bg-glow/20 blur-3xl"></div>
                <div x-parallax="{ scroll: -0.25, pointer: -40 }" class="absolute -right-20 bottom-0 -z-10 size-96 rounded-full bg-brand/25 blur-3xl"></div>

                <div class="mx-auto max-w-5xl">
                    <h2 x-reveal class="text-center text-3xl font-bold md:text-5xl">Pilih paket Anda</h2>
                    <p x-reveal class="mx-auto mt-3 max-w-md text-center text-muted">Aktif selama 30 hari. Bisa ganti paket kapan saja.</p>
                    <div class="mt-12">
                        @include('plans.partials.grid', ['plans' => $plans, 'actionRoute' => 'register'])
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-3xl px-5 py-20" aria-labelledby="faq-heading">
                <h2 id="faq-heading" x-reveal class="mb-8 text-center text-3xl font-bold md:text-4xl">Pertanyaan umum</h2>
                <div class="space-y-3" x-data="{ opened: 0 }">
                    @foreach ($faqs as $question => $answer)
                        <div x-reveal class="overflow-hidden rounded-2xl border border-white/10 bg-card">
                            <button type="button" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-medium"
                                    @click="opened = opened === {{ $loop->index }} ? null : {{ $loop->index }}" :aria-expanded="opened === {{ $loop->index }}">
                                {{ $question }}
                                <x-icon name="plus" class="shrink-0 transition" x-bind:class="opened === {{ $loop->index }} && 'rotate-45'" />
                            </button>
                            <p x-show="opened === {{ $loop->index }}" x-cloak x-transition.opacity class="px-5 pb-5 text-sm leading-relaxed text-muted">{{ $answer }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10 px-5 py-8 text-center text-sm text-muted">
            <x-wordmark class="mb-2 text-white" />
            <p>Proyek kuliah. Semua judul dan gambar dalam demo ini fiktif.</p>
        </footer>
    </body>
</html>
