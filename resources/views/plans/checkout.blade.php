<x-guest-layout>
    <h1 class="text-2xl font-bold">Konfirmasi paket</h1>
    <p class="mt-1 text-sm text-muted">Ini pembayaran simulasi: tidak ada biaya yang ditagih.</p>

    <dl class="mt-6 space-y-3 rounded-2xl bg-white/5 p-5 text-sm">
        <div class="flex justify-between gap-4"><dt class="text-muted">Paket</dt><dd class="font-semibold">{{ $plan->name }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="text-muted">Kualitas</dt><dd>{{ $plan->quality }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="text-muted">Jumlah layar</dt><dd>{{ $plan->max_screens }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="text-muted">Masa aktif</dt><dd>{{ $plan->duration_days }} hari</dd></div>
        <div class="flex justify-between gap-4 border-t border-white/10 pt-3 text-base"><dt class="font-semibold">Total</dt><dd class="font-bold">{{ $plan->formattedPrice() }}</dd></div>
    </dl>

    <form method="POST" action="{{ route('checkout.store', $plan) }}" class="mt-6 flex flex-col gap-3">
        @csrf
        <x-primary-button class="w-full py-3">Bayar {{ $plan->formattedPrice() }}</x-primary-button>
        <a href="{{ route('plans.index') }}" class="text-center text-sm text-muted underline">Ganti paket</a>
    </form>
</x-guest-layout>
