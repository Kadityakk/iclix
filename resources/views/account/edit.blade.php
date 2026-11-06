<x-app-layout title="Akun">
    <div class="mx-auto max-w-3xl space-y-6 px-4 pt-24 sm:px-6">
        <h1 class="text-3xl font-bold">Akun &amp; langganan</h1>

        <section class="space-y-4 rounded-2xl border border-white/10 bg-card p-6" aria-labelledby="subscription-heading">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 id="subscription-heading" class="text-lg font-medium">Langganan</h2>
                <a href="{{ route('plans.index') }}" class="rounded-full border border-white/25 px-4 py-1.5 text-sm font-medium transition hover:bg-white/10">
                    {{ $activeSubscription ? 'Ganti paket' : 'Pilih paket' }}
                </a>
            </div>

            @if ($activeSubscription)
                <p class="text-sm text-muted">
                    Paket <span class="font-semibold text-body">{{ $activeSubscription->plan->name }}</span> aktif sampai
                    <span class="font-semibold text-body">{{ $activeSubscription->ends_at->translatedFormat('j F Y') }}</span>.
                </p>
            @else
                <p class="text-sm text-muted">Belum ada langganan aktif.</p>
            @endif

            @if ($subscriptions->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[32rem] text-left text-sm">
                        <caption class="sr-only">Riwayat transaksi</caption>
                        <thead class="text-xs text-muted uppercase">
                            <tr class="border-b border-white/10">
                                <th class="py-2 pr-4 font-medium">Invoice</th>
                                <th class="py-2 pr-4 font-medium">Paket</th>
                                <th class="py-2 pr-4 font-medium">Jumlah</th>
                                <th class="py-2 pr-4 font-medium">Berakhir</th>
                                <th class="py-2 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subscriptions as $subscription)
                                <tr class="border-b border-white/5">
                                    <td class="py-2 pr-4 font-mono text-xs">{{ $subscription->invoice_code }}</td>
                                    <td class="py-2 pr-4">{{ $subscription->plan->name }}</td>
                                    <td class="py-2 pr-4">Rp{{ number_format($subscription->amount, 0, ',', '.') }}</td>
                                    <td class="py-2 pr-4">{{ $subscription->ends_at->translatedFormat('j M Y') }}</td>
                                    <td class="py-2">{{ $subscription->isCurrentlyActive() ? 'Aktif' : 'Berakhir' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <div class="rounded-2xl border border-white/10 bg-card p-6">
            @include('account.partials.update-profile-information-form')
        </div>

        <div class="rounded-2xl border border-white/10 bg-card p-6">
            @include('account.partials.update-password-form')
        </div>

        <div class="rounded-2xl border border-white/10 bg-card p-6">
            @include('account.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
