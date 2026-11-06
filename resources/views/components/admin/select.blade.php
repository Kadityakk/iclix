<select {{ $attributes->merge(['class' => 'w-full rounded-xl border border-white/15 bg-card px-4 py-2.5 text-sm text-body focus:border-brand-hover focus:ring-2 focus:ring-brand/40 focus:outline-none']) }}>
    {{ $slot }}
</select>
