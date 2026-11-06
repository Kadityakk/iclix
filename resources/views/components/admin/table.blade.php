<div class="overflow-x-auto rounded-2xl border border-white/10 bg-card">
    <table {{ $attributes->merge(['class' => 'w-full min-w-[40rem] text-left text-sm']) }}>
        {{ $slot }}
    </table>
</div>
