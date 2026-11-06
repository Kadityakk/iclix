<x-admin-layout>
    <x-admin.page-header :title="'Musim: '.$content->title" :back-url="route('admin.contents.index')" :create-url="route('admin.contents.seasons.create', $content)" create-label="Tambah musim" />

    <x-admin.table>
        <thead class="text-xs text-muted uppercase">
            <tr class="border-b border-white/10">
                <th class="px-4 py-3 font-medium">Nomor</th>
                <th class="px-4 py-3 font-medium">Judul</th>
                <th class="px-4 py-3 font-medium">Episode</th>
                <th class="px-4 py-3 text-right font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($seasons as $season)
                <tr class="border-b border-white/5 last:border-0">
                    <td class="px-4 py-3">{{ $season->season_number }}</td>
                    <td class="px-4 py-3 font-medium">{{ $season->displayTitle() }}</td>
                    <td class="px-4 py-3 text-muted">{{ $season->episodes_count }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.seasons.episodes.index', $season) }}" class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-medium transition hover:bg-white/20">Kelola episode</a>
                            <x-admin.edit-link :href="route('admin.seasons.edit', $season)" />
                            <x-admin.delete-button :action="route('admin.seasons.destroy', $season)" :label="'Hapus '.$season->displayTitle()" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-10 text-center text-muted">Belum ada musim.</td></tr>
            @endforelse
        </tbody>
    </x-admin.table>
</x-admin-layout>
