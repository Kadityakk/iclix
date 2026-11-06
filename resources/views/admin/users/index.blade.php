<x-admin-layout>
    <x-admin.page-header title="Pengguna" />

    <form method="GET" class="mb-4" role="search">
        <x-text-input type="search" name="q" :value="request('q')" placeholder="Cari nama atau email..." aria-label="Cari pengguna" class="max-w-xs" />
    </form>

    <x-admin.table>
        <thead class="text-xs text-muted uppercase">
            <tr class="border-b border-white/10">
                <th class="px-4 py-3 font-medium">Nama</th>
                <th class="px-4 py-3 font-medium">Email</th>
                <th class="px-4 py-3 font-medium">Peran</th>
                <th class="px-4 py-3 font-medium">Langganan</th>
                <th class="px-4 py-3 font-medium">Status</th>
                <th class="px-4 py-3 text-right font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr class="border-b border-white/5 last:border-0">
                    <td class="px-4 py-3 font-medium"><a href="{{ route('admin.users.show', $user) }}" class="hover:underline">{{ $user->name }}</a></td>
                    <td class="px-4 py-3 text-muted">{{ $user->email }}</td>
                    <td class="px-4 py-3 text-muted">{{ $user->isAdmin() ? 'Admin' : 'Member' }}</td>
                    <td class="px-4 py-3 text-muted">{{ $user->activeSubscription?->plan->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span @class(['rounded-full px-2.5 py-1 text-xs font-medium', 'bg-emerald-500/15 text-emerald-300' => $user->is_active, 'bg-rose-500/15 text-rose-300' => ! $user->is_active])>
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @unless ($user->is(auth()->user()))
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                <button class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-medium transition hover:bg-white/20">{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-muted">Pengguna tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <div class="mt-4">{{ $users->links() }}</div>
</x-admin-layout>
