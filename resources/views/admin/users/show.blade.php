<x-admin-layout>
    <x-admin.page-header :title="$user->name" :back-url="route('admin.users.index')" />

    <div class="grid gap-4 md:grid-cols-2">
        <dl class="space-y-2 rounded-2xl border border-white/10 bg-card p-5 text-sm">
            <div class="flex justify-between gap-3"><dt class="text-muted">Email</dt><dd>{{ $user->email }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-muted">Peran</dt><dd>{{ $user->isAdmin() ? 'Admin' : 'Member' }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-muted">Status</dt><dd>{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-muted">Terdaftar</dt><dd>{{ $user->created_at->translatedFormat('j F Y') }}</dd></div>
        </dl>

        <div class="rounded-2xl border border-white/10 bg-card p-5">
            <h2 class="mb-3 text-sm font-medium text-muted">Profil</h2>
            <ul class="flex flex-wrap gap-3">
                @forelse ($user->profiles as $profile)
                    <li class="flex items-center gap-2 text-sm"><x-avatar :profile="$profile" class="size-8 text-xs" /> {{ $profile->name }}</li>
                @empty
                    <li class="text-sm text-muted">Belum ada profil.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <h2 class="mt-8 mb-3 text-lg font-semibold">Transaksi</h2>
    <x-admin.table>
        <thead class="text-xs text-muted uppercase">
            <tr class="border-b border-white/10">
                <th class="px-4 py-3 font-medium">Invoice</th>
                <th class="px-4 py-3 font-medium">Paket</th>
                <th class="px-4 py-3 font-medium">Jumlah</th>
                <th class="px-4 py-3 font-medium">Berakhir</th>
                <th class="px-4 py-3 font-medium">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($subscriptions as $subscription)
                <tr class="border-b border-white/5 last:border-0">
                    <td class="px-4 py-3 font-mono text-xs">{{ $subscription->invoice_code }}</td>
                    <td class="px-4 py-3">{{ $subscription->plan->name }}</td>
                    <td class="px-4 py-3">Rp{{ number_format($subscription->amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-muted">{{ $subscription->ends_at->translatedFormat('j M Y') }}</td>
                    <td class="px-4 py-3 text-muted">{{ $subscription->isCurrentlyActive() ? 'Aktif' : 'Berakhir' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-muted">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </x-admin.table>
</x-admin-layout>
