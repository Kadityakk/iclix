<x-admin-layout>
    <x-admin.page-header :title="$season->content->title.': '.$season->displayTitle()" :back-url="route('admin.contents.seasons.index', $season->content)" :create-url="route('admin.seasons.episodes.create', $season)" create-label="Tambah episode" />

    <x-admin.table>
        <thead class="text-xs text-muted uppercase">
            <tr class="border-b border-white/10">
                <th class="px-4 py-3 font-medium">No.</th>
                <th class="px-4 py-3 font-medium">Judul</th>
                <th class="px-4 py-3 font-medium">Durasi</th>
                <th class="px-4 py-3 text-right font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($episodes as $episode)
                <tr class="border-b border-white/5 last:border-0">
                    <td class="px-4 py-3">{{ $episode->episode_number }}</td>
                    <td class="px-4 py-3 font-medium">{{ $episode->title }}</td>
                    <td class="px-4 py-3 text-muted">{{ $episode->duration_min }} menit</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <x-admin.edit-link :href="route('admin.episodes.edit', $episode)" />
                            <x-admin.delete-button :action="route('admin.episodes.destroy', $episode)" :label="'Hapus '.$episode->title" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-10 text-center text-muted">Belum ada episode.</td></tr>
            @endforelse
        </tbody>
    </x-admin.table>
</x-admin-layout>
