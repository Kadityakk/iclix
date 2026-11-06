@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl bg-emerald-500/10 px-4 py-2 text-sm font-medium text-emerald-300']) }}>
        {{ $status }}
    </div>
@endif
