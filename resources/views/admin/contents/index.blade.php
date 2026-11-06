<x-admin-layout>
    <x-admin.page-header title="Konten" :create-url="route('admin.contents.create')" create-label="Tambah konten" />

    <form method="GET" class="mb-4 flex flex-wrap gap-3" role="search">
        <x-text-input type="search" name="q" :value="request('q')" placeholder="Cari judul..." aria-label="Cari judul" class="max-w-xs" />
        <x-admin.select name="type" aria-label="Jenis" class="w-auto" x-data @change="$el.form.submit()">
            <option value="">Semua jenis</option>
            <option value="movie" @selected(request('type') === 'movie')>Film</option>
            <option value="series" @selected(request('type') === 'series')>Serial</option>
        </x-admin.select>
    </form>

    <x-admin.table>
        <thead class="text-xs text-muted uppercase">
            <tr class="border-b border-white/10">
                <th class="px-4 py-3 font-medium">Judul</th>
                <th class="px-4 py-3 font-medium">Jenis</th>
                <th class="px-4 py-3 font-medium">Tahun</th>
                <th class="px-4 py-3 font-medium">Genre</th>
                <th class="px-4 py-3 font-medium">Unggulan</th>
                <th class="px-4 py-3 text-right font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contents as $content)
                <tr class="border-b border-white/5 last:border-0">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="block h-14 w-10 shrink-0 overflow-hidden rounded bg-navy">
                                @if ($content->posterUrl())
                                    <img src="{{ $content->posterUrl() }}" alt="Poster {{ $content->title }}" loading="lazy" class="size-full object-cover">
                                @endif
                            </span>
                            <span class="font-medium">{{ $content->title }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-muted">{{ $content->type->label() }}</td>
                    <td class="px-4 py-3 text-muted">{{ $content->release_year }}</td>
                    <td class="px-4 py-3 text-muted">{{ $content->genres->pluck('name')->join(', ') }}</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.contents.featured', $content) }}">
                            @csrf
                            @method('PATCH')
                            <button @class(['rounded-full px-3 py-1 text-xs font-medium transition', 'bg-brand text-white' => $content->is_featured, 'bg-white/10 text-muted hover:bg-white/20' => ! $content->is_featured])>
                                {{ $content->is_featured ? 'Unggulan' : 'Tandai' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            @if ($content->isSeries())
                                <a href="{{ route('admin.contents.seasons.index', $content) }}" class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-medium transition hover:bg-white/20">Musim ({{ $content->seasons_count }})</a>
                            @endif
                            <x-admin.edit-link :href="route('admin.contents.edit', $content)" :label="'Ubah '.$content->title" />
                            <x-admin.delete-button :action="route('admin.contents.destroy', $content)" :label="'Hapus '.$content->title" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-muted">Belum ada konten.</td></tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <div class="mt-4">{{ $contents->links() }}</div>
</x-admin-layout>
