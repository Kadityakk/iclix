<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-full bg-brand px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-hover focus-visible:outline-offset-2 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
