<x-guest-wide title="Pilih paket">
    <div class="mx-auto max-w-5xl">
        <h1 class="text-center text-3xl font-bold md:text-5xl">Pilih paket Anda</h1>

        @if ($activeSubscription)
            <p class="mt-3 text-center text-muted">
                Paket {{ $activeSubscription->plan->name }} aktif sampai {{ $activeSubscription->ends_at->translatedFormat('j F Y') }}.
                <a href="{{ route('browse') }}" class="font-medium text-brand-hover underline">Kembali menonton</a>
            </p>
        @else
            <p class="mt-3 text-center text-muted">Aktifkan paket untuk mulai menonton.</p>
        @endif

        <div class="mt-12">
            @include('plans.partials.grid', ['plans' => $plans, 'actionRoute' => 'checkout'])
        </div>
    </div>
</x-guest-wide>
