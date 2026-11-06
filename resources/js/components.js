import { csrfToken, requestJson } from './http';

const HERO_SLIDE_MS = 8000;
const HERO_TICK_MS = 50;
const HERO_TEXT_FADE_MS = 250;
const PROGRESS_SAVE_MS = 10000;
const CONTROLS_HIDE_MS = 3000;

/**
 * Hero banner: auto-advances every 8 seconds and pauses while hovered.
 */
export function heroSlider(items = []) {
    return {
        items,
        active: 0,
        visibleIndex: 0,
        isTextVisible: true,
        isPaused: false,
        elapsedMs: 0,
        timer: null,

        init() {
            this.timer = setInterval(() => this.tick(), HERO_TICK_MS);
        },

        destroy() {
            clearInterval(this.timer);
        },

        get current() {
            return this.items[this.visibleIndex] ?? {};
        },

        get progressPercent() {
            return Math.min((this.elapsedMs / HERO_SLIDE_MS) * 100, 100);
        },

        tick() {
            if (this.isPaused || this.items.length < 2 || document.hidden) {
                return;
            }

            this.elapsedMs += HERO_TICK_MS;

            if (this.elapsedMs >= HERO_SLIDE_MS) {
                this.goTo(this.active + 1);
            }
        },

        goTo(index) {
            const total = this.items.length;

            if (total === 0) {
                return;
            }

            this.active = (index + total) % total;
            this.elapsedMs = 0;
            this.isTextVisible = false;

            setTimeout(() => {
                this.visibleIndex = this.active;
                this.isTextVisible = true;
            }, HERO_TEXT_FADE_MS);
        },
    };
}

/**
 * Horizontally scrolling row with arrow buttons.
 */
export function contentRow() {
    return {
        canScrollLeft: false,
        canScrollRight: false,

        init() {
            this.$nextTick(() => this.updateArrows());
        },

        updateArrows() {
            const track = this.$refs.track;

            if (!track) {
                return;
            }

            this.canScrollLeft = track.scrollLeft > 8;
            this.canScrollRight = track.scrollLeft + track.clientWidth < track.scrollWidth - 8;
        },

        scrollRow(direction) {
            const track = this.$refs.track;
            track.scrollBy({ left: direction * track.clientWidth * 0.8, behavior: 'smooth' });
        },
    };
}

/**
 * Debounced title search shared by the search pill and the search page.
 */
export function liveSearch(initialQuery = '', initialResults = []) {
    return {
        query: initialQuery,
        results: initialResults,
        isLoading: false,
        hasSearched: initialQuery !== '',
        isOpen: false,
        controller: null,

        async search(syncUrl = false) {
            const term = this.query.trim();
            this.controller?.abort();

            if (syncUrl) {
                const url = new URL(window.location.href);
                term ? url.searchParams.set('q', term) : url.searchParams.delete('q');
                window.history.replaceState({}, '', url);
            }

            if (term === '') {
                this.results = [];
                this.hasSearched = false;
                this.isLoading = false;
                return;
            }

            this.controller = new AbortController();
            this.isLoading = true;

            try {
                const { results } = await requestJson(`search?q=${encodeURIComponent(term)}`, {
                    signal: this.controller.signal,
                });
                this.results = results;
                this.hasSearched = true;
                this.isLoading = false;
            } catch (error) {
                if (error.name !== 'AbortError') {
                    this.isLoading = false;
                    this.$store.toast.show('Pencarian gagal. Coba lagi.');
                }
            }
        },
    };
}

/**
 * HTML5 player with custom controls and resume support; saves progress every 10 seconds.
 */
export function videoPlayer({ contentId, episodeId = null, resumeFrom = 0, nextUrl = null }) {
    return {
        isPlaying: false,
        isMuted: false,
        volume: 1,
        currentTime: 0,
        duration: 0,
        bufferedPercent: 0,
        isFullscreen: false,
        areControlsVisible: true,
        hasEnded: false,
        hasError: false,
        hideTimer: null,
        saveTimer: null,
        lastSavedPosition: -1,
        nextUrl,

        init() {
            this.saveTimer = setInterval(() => this.saveProgress(), PROGRESS_SAVE_MS);
            window.addEventListener('pagehide', () => this.saveProgress(true));
            document.addEventListener('fullscreenchange', () => {
                this.isFullscreen = document.fullscreenElement !== null;
            });
            this.showControls();
        },

        destroy() {
            clearInterval(this.saveTimer);
            clearTimeout(this.hideTimer);
        },

        onMetadataLoaded() {
            const video = this.$refs.video;
            this.duration = video.duration;

            if (resumeFrom > 0 && resumeFrom < video.duration) {
                video.currentTime = resumeFrom;
            }

            video.play().catch(() => (this.isPlaying = false));
        },

        onTimeUpdate() {
            const video = this.$refs.video;
            this.currentTime = video.currentTime;

            if (video.buffered.length > 0 && video.duration) {
                this.bufferedPercent = (video.buffered.end(video.buffered.length - 1) / video.duration) * 100;
            }
        },

        onPause() {
            this.isPlaying = false;
            this.areControlsVisible = true;
            this.saveProgress();
        },

        onEnded() {
            this.hasEnded = true;
            this.saveProgress();
        },

        togglePlay() {
            const video = this.$refs.video;
            this.hasEnded = false;
            video.paused ? video.play() : video.pause();
        },

        seekTo(seconds) {
            this.$refs.video.currentTime = Math.min(Math.max(seconds, 0), this.duration || 0);
        },

        seekBy(seconds) {
            this.seekTo(this.$refs.video.currentTime + seconds);
        },

        setVolume(value) {
            const video = this.$refs.video;
            video.volume = Math.min(Math.max(value, 0), 1);
            video.muted = video.volume === 0;
            this.volume = video.volume;
            this.isMuted = video.muted;
        },

        toggleMute() {
            const video = this.$refs.video;
            video.muted = !video.muted;
            this.isMuted = video.muted;
        },

        toggleFullscreen() {
            document.fullscreenElement ? document.exitFullscreen() : this.$root.requestFullscreen();
        },

        showControls() {
            this.areControlsVisible = true;
            clearTimeout(this.hideTimer);
            this.hideTimer = setTimeout(() => {
                if (this.isPlaying) {
                    this.areControlsVisible = false;
                }
            }, CONTROLS_HIDE_MS);
        },

        get progressPercent() {
            return this.duration ? (this.currentTime / this.duration) * 100 : 0;
        },

        formatTime(totalSeconds) {
            const seconds = Math.floor(totalSeconds || 0);
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const remainder = String(seconds % 60).padStart(2, '0');

            return hours > 0
                ? `${hours}:${String(minutes).padStart(2, '0')}:${remainder}`
                : `${minutes}:${remainder}`;
        },

        saveProgress(isLeavingPage = false) {
            const position = Math.floor(this.$refs.video?.currentTime ?? 0);

            if (!this.duration || position === this.lastSavedPosition) {
                return;
            }

            this.lastSavedPosition = position;

            requestJson('progress', {
                method: 'POST',
                keepalive: isLeavingPage,
                body: {
                    content_id: contentId,
                    episode_id: episodeId,
                    position_sec: position,
                    duration_sec: Math.floor(this.duration),
                },
            }).catch(() => (this.lastSavedPosition = -1));
        },
    };
}

/**
 * Submits a multipart form over XHR so large uploads can show real progress.
 */
export function uploadForm() {
    return {
        isUploading: false,
        progressPercent: 0,
        errors: {},
        generalError: '',

        hasSelectedFile(form) {
            return [...form.querySelectorAll('input[type="file"]')].some((input) => input.files.length > 0);
        },

        submit(event) {
            const form = event.target;

            if (!this.hasSelectedFile(form)) {
                return;
            }

            event.preventDefault();
            this.isUploading = true;
            this.progressPercent = 0;
            this.errors = {};
            this.generalError = '';

            const request = new XMLHttpRequest();
            request.open('POST', form.action);
            request.setRequestHeader('Accept', 'application/json');
            request.setRequestHeader('X-CSRF-TOKEN', csrfToken());
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            request.upload.addEventListener('progress', (progress) => {
                if (progress.lengthComputable) {
                    this.progressPercent = Math.round((progress.loaded / progress.total) * 100);
                }
            });

            request.addEventListener('load', () => this.handleResponse(request));
            request.addEventListener('error', () => this.fail('Koneksi terputus saat mengunggah.'));
            request.send(new FormData(form));
        },

        handleResponse(request) {
            if (request.status === 422) {
                this.isUploading = false;
                this.errors = JSON.parse(request.responseText).errors ?? {};
                this.$nextTick(() => this.$root.querySelector('[data-upload-error]')?.scrollIntoView({ block: 'center' }));
                return;
            }

            if (request.status === 413) {
                this.fail('File melebihi batas upload server. Naikkan upload_max_filesize dan post_max_size di php.ini.');
                return;
            }

            if (request.status >= 400) {
                this.fail('Upload gagal. Coba lagi.');
                return;
            }

            window.location.assign(request.responseURL);
        },

        fail(message) {
            this.isUploading = false;
            this.generalError = message;
        },

        errorFor(field) {
            return this.errors[field]?.[0] ?? '';
        },
    };
}
