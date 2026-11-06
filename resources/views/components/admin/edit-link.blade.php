@props(['href', 'label' => 'Ubah'])

<a href="{{ $href }}" class="inline-flex size-9 items-center justify-center rounded-full bg-white/5 text-muted transition hover:bg-white/15 hover:text-white" aria-label="{{ $label }}" title="{{ $label }}">
    <x-icon name="pencil" class="size-4" />
</a>
