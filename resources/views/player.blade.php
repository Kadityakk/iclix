@php
$streamUrl = $episode ? route('stream', [$content, $episode]) : route('stream', $content);
$heading = $episode ? "{$content->title}: {$episode->title}" : $content->title;
$nextUrl = $nextEpisode ? route('watch', [$content, $nextEpisode]) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark h-full">
    <head>
        @include('partials.head', ['title' => $heading])
    </head>
    <body class="h-full bg-black font-sans antialiased">
        <main x-data="videoPlayer(@js(['contentId' => $content->id, 'episodeId' => $episode?->id, 'resumeFrom' => $resumeFrom, 'nextUrl' => $nextUrl]))"
              @mousemove="showControls()" @touchstart.passive="showControls()"
              @keydown.window.space.prevent="togglePlay()"
              @keydown.window.left="seekBy(-10)" @keydown.window.right="seekBy(10)"
              @keydown.window.m="toggleMute()" @keydown.window.f="toggleFullscreen()"
              class="relative h-full overflow-hidden bg-black" :class="areControlsVisible || 'cursor-none'">
            <video x-ref="video" src="{{ $streamUrl }}" preload="metadata" playsinline class="size-full" @if ($content->backdropUrl()) poster="{{ $content->backdropUrl() }}" @endif
                   @loadedmetadata="onMetadataLoaded()" @timeupdate="onTimeUpdate()" @click="togglePlay()"
                   @play="isPlaying = true; showControls()" @pause="onPause()" @ended="onEnded()" x-on:error="hasError = true"></video>

            <div x-show="hasError" x-cloak class="absolute inset-0 flex items-center justify-center bg-black/80 p-6 text-center">
                <p class="max-w-sm text-white/85">Video tidak bisa diputar. File mungkin belum tersedia.</p>
            </div>

            <div x-show="hasEnded && nextUrl" x-cloak class="absolute right-6 bottom-28 z-10">
                <a :href="nextUrl" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-black">
                    <x-icon name="play" class="size-4" /> Episode berikutnya
                </a>
            </div>

            <div x-show="areControlsVisible" x-transition.opacity class="absolute inset-x-0 top-0 flex items-center gap-4 bg-gradient-to-b from-black/80 to-transparent p-4 md:p-6">
                <a href="{{ route('contents.show', $content) }}" class="flex size-11 shrink-0 items-center justify-center rounded-full bg-white/10 transition hover:bg-white/25" aria-label="Kembali ke detail">
                    <x-icon name="left" />
                </a>
                <h1 class="truncate text-lg font-semibold">{{ $heading }}</h1>
            </div>

            <div x-show="areControlsVisible" x-transition.opacity class="absolute inset-x-0 bottom-0 space-y-2 bg-gradient-to-t from-black/90 to-transparent p-4 md:p-6">
                <div class="relative flex h-5 items-center">
                    <div class="pointer-events-none absolute inset-x-0 h-1 rounded-full bg-white/25">
                        <div class="h-full rounded-full bg-white/30" :style="`width: ${bufferedPercent}%`"></div>
                    </div>
                    <div class="pointer-events-none absolute left-0 h-1 rounded-full bg-brand-hover" :style="`width: ${progressPercent}%`"></div>
                    <input type="range" min="0" :max="duration" step="1" :value="currentTime" @input="seekTo(Number($event.target.value))" aria-label="Posisi video"
                           class="relative h-5 w-full cursor-pointer appearance-none bg-transparent accent-white">
                </div>

                <div class="flex items-center gap-1 sm:gap-2">
                    <button type="button" @click="togglePlay()" class="flex size-11 items-center justify-center rounded-full transition hover:bg-white/15" :aria-label="isPlaying ? 'Jeda' : 'Putar'">
                        <span x-show="!isPlaying"><x-icon name="play" class="size-6" /></span>
                        <span x-show="isPlaying" x-cloak><x-icon name="pause" class="size-6" /></span>
                    </button>
                    <button type="button" @click="seekBy(-10)" class="flex size-11 items-center justify-center rounded-full transition hover:bg-white/15" aria-label="Mundur 10 detik">
                        <x-icon name="back10" />
                    </button>
                    <button type="button" @click="seekBy(10)" class="flex size-11 items-center justify-center rounded-full transition hover:bg-white/15" aria-label="Maju 10 detik">
                        <x-icon name="forward10" />
                    </button>
                    <button type="button" @click="toggleMute()" class="flex size-11 items-center justify-center rounded-full transition hover:bg-white/15" :aria-label="isMuted ? 'Nyalakan suara' : 'Matikan suara'">
                        <span x-show="!isMuted"><x-icon name="volume" /></span>
                        <span x-show="isMuted" x-cloak><x-icon name="mute" /></span>
                    </button>
                    <input type="range" min="0" max="1" step="0.05" :value="isMuted ? 0 : volume" @input="setVolume(Number($event.target.value))" aria-label="Volume" class="hidden w-24 cursor-pointer accent-white sm:block">

                    <span class="ml-2 text-xs text-white/80 tabular-nums sm:text-sm" x-text="`${formatTime(currentTime)} / ${formatTime(duration)}`"></span>

                    <span class="flex-1"></span>

                    @if ($nextUrl)
                        <a href="{{ $nextUrl }}" class="hidden items-center gap-1 rounded-full px-3 py-2 text-sm font-medium transition hover:bg-white/15 sm:inline-flex">
                            Episode berikutnya <x-icon name="right" class="size-4" />
                        </a>
                    @endif

                    <button type="button" @click="toggleFullscreen()" class="flex size-11 items-center justify-center rounded-full transition hover:bg-white/15" aria-label="Layar penuh">
                        <x-icon name="fullscreen" />
                    </button>
                </div>
            </div>
        </main>
    </body>
</html>
