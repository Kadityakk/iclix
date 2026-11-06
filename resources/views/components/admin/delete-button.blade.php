@props(['action', 'label'])

<form method="POST" action="{{ $action }}" class="inline" x-data @submit="confirm(@js($label.'?')) || $event.preventDefault()">
    @csrf
    @method('DELETE')
    <button class="flex size-9 items-center justify-center rounded-full bg-white/5 text-muted transition hover:bg-rose-600 hover:text-white" aria-label="{{ $label }}" title="{{ $label }}">
        <x-icon name="trash" class="size-4" />
    </button>
</form>
