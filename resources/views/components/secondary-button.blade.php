<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 rounded-full border border-white/25 px-6 py-2.5 text-sm font-semibold text-body transition hover:bg-white/10 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
