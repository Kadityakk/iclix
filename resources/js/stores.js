import { requestJson } from './http';

/**
 * @param {import('alpinejs').Alpine} Alpine
 */
export function registerStores(Alpine) {
    Alpine.store('toast', {
        message: '',
        isVisible: false,
        hideTimer: null,

        show(message) {
            this.message = message;
            this.isVisible = true;
            clearTimeout(this.hideTimer);
            this.hideTimer = setTimeout(() => (this.isVisible = false), 3000);
        },
    });

    Alpine.store('myList', {
        ids: window.iClix?.watchlistIds ?? [],

        has(contentId) {
            return this.ids.includes(contentId);
        },

        async toggle(contentId) {
            const wasSaved = this.has(contentId);
            this.ids = wasSaved ? this.ids.filter((id) => id !== contentId) : [...this.ids, contentId];

            try {
                const { message } = await requestJson(`my-list/${contentId}`, { method: wasSaved ? 'DELETE' : 'POST' });
                Alpine.store('toast').show(message);
            } catch {
                this.ids = wasSaved ? [...this.ids, contentId] : this.ids.filter((id) => id !== contentId);
                Alpine.store('toast').show('Gagal menyimpan. Coba lagi.');
            }
        },
    });

    Alpine.store('ratings', {
        values: {},

        seed(contentIds, value) {
            for (const contentId of contentIds) {
                this.values[contentId] = value;
            }
        },

        valueOf(contentId) {
            return this.values[contentId] ?? 0;
        },

        async rate(contentId, value) {
            const previous = this.valueOf(contentId);
            const next = previous === value ? 0 : value;
            this.values[contentId] = next;

            try {
                await requestJson(`ratings/${contentId}`, { method: 'POST', body: { value: next } });

                if (next !== 0) {
                    Alpine.store('toast').show(next > 0 ? 'Anda menyukai judul ini' : 'Anda tidak menyukai judul ini');
                }
            } catch {
                this.values[contentId] = previous;
                Alpine.store('toast').show('Gagal menyimpan penilaian.');
            }
        },
    });

    Alpine.store('detail', {
        content: null,
        trailerUrl: null,

        open(content) {
            this.trailerUrl = null;
            this.content = content;
        },

        openTrailer(url) {
            this.content = null;
            this.trailerUrl = url;
        },

        close() {
            this.content = null;
            this.trailerUrl = null;
        },

        get isOpen() {
            return this.content !== null || this.trailerUrl !== null;
        },
    });
}
