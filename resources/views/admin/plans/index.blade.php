<x-admin-layout>
    <x-admin.page-header title="Paket langganan" :create-url="route('admin.plans.create')" create-label="Tambah paket" />

    <x-admin.table>
        <thead class="text-xs text-muted uppercase">
            <tr class="border-b border-white/10">
                <th class="px-4 py-3 font-medium">Nama</th>
                <th class="px-4 py-3 font-medium">Harga</th>
                <th class="px-4 py-3 font-medium">Kualitas</th>
                <th class="px-4 py-3 font-medium">Layar</th>
                <th class="px-4 py-3 font-medium">Durasi</th>
                <th class="px-4 py-3 font-medium">Transaksi</th>
                <th class="px-4 py-3 text-right font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($plans as $plan)
                <tr class="border-b border-white/5 last:border-0">
                    <td class="px-4 py-3 font-medium">{{ $plan->name }}</td>
                    <td class="px-4 py-3">{{ $plan->formattedPrice() }}</td>
                    <td class="px-4 py-3 text-muted">{{ $plan->quality }}</td>
                    <td class="px-4 py-3 text-muted">{{ $plan->max_screens }}</td>
                    <td class="px-4 py-3 text-muted">{{ $plan->duration_days }} hari</td>
                    <td class="px-4 py-3 text-muted">{{ $plan->subscriptions_count }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <x-admin.edit-link :href="route('admin.plans.edit', $plan)" :label="'Ubah '.$plan->name" />
                            <x-admin.delete-button :action="route('admin.plans.destroy', $plan)" :label="'Hapus '.$plan->name" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-muted">Belum ada paket.</td></tr>
            @endforelse
        </tbody>
    </x-admin.table>
</x-admin-layout>
