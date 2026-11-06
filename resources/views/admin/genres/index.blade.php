<x-admin-layout>
    <x-admin.page-header title="Genre" :create-url="route('admin.genres.create')" create-label="Tambah genre" />

    <x-admin.table>
        <thead class="text-xs text-muted uppercase">
            <tr class="border-b border-white/10">
                <th class="px-4 py-3 font-medium">Nama</th>
                <th class="px-4 py-3 font-medium">Slug</th>
                <th class="px-4 py-3 font-medium">Konten</th>
                <th class="px-4 py-3 text-right font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($genres as $genre)
                <tr class="border-b border-white/5 last:border-0">
                    <td class="px-4 py-3 font-medium">{{ $genre->name }}</td>
                    <td class="px-4 py-3 text-muted">{{ $genre->slug }}</td>
                    <td class="px-4 py-3 text-muted">{{ $genre->contents_count }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <x-admin.edit-link :href="route('admin.genres.edit', $genre)" :label="'Ubah '.$genre->name" />
                            <x-admin.delete-button :action="route('admin.genres.destroy', $genre)" :label="'Hapus '.$genre->name" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-10 text-center text-muted">Belum ada genre.</td></tr>
            @endforelse
        </tbody>
    </x-admin.table>
</x-admin-layout>
