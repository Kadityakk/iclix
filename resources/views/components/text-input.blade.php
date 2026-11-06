@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm text-body placeholder:text-muted/70 focus:border-brand-hover focus:outline-none focus:ring-2 focus:ring-brand/40 disabled:opacity-50']) }}>
