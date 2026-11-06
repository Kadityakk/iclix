<div class="grid gap-5 md:grid-cols-3">
    @foreach ($plans as $plan)
        @php($isRecommended = $loop->iteration === 2)
        <article x-reveal style="transition-delay: {{ $loop->index * 100 }}ms"
                 @class([
                     'relative flex flex-col rounded-3xl border p-7',
                     'border-brand-hover bg-gradient-to-b from-brand/25 to-card shadow-xl shadow-brand/20' => $isRecommended,
                     'border-white/10 bg-card' => ! $isRecommended,
                 ])>
            @if ($isRecommended)
                <span class="absolute -top-3 left-7 rounded-full bg-brand px-3 py-1 text-xs font-semibold">Paling populer</span>
            @endif

            <h3 class="text-xl font-semibold">{{ $plan->name }}</h3>
            <p class="mt-3 text-3xl font-bold">{{ $plan->formattedPrice() }}<span class="text-sm font-normal text-muted"> / {{ $plan->duration_days }} hari</span></p>

            <ul class="mt-6 flex-1 space-y-3 text-sm">
                <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-emerald-400" /> Kualitas {{ $plan->quality }}</li>
                <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-emerald-400" /> {{ $plan->max_screens }} layar</li>
                <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-emerald-400" /> Sampai 4 profil</li>
            </ul>

            <a href="{{ $actionRoute === 'register' ? route('register') : route('checkout.show', $plan) }}"
               @class([
                   'mt-7 rounded-full px-5 py-3 text-center text-sm font-semibold transition',
                   'bg-white text-black hover:bg-white/85' => $isRecommended,
                   'border border-white/30 hover:bg-white/10' => ! $isRecommended,
               ])>
                Pilih {{ $plan->name }}
            </a>
        </article>
    @endforeach
</div>
