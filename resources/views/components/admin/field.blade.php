@props(['name', 'label', 'hint' => null])

<div {{ $attributes }}>
    <x-input-label :for="$name" :value="$label" />
    <div class="mt-1">{{ $slot }}</div>
    @if ($hint)
        <p class="mt-1 text-xs text-muted">{{ $hint }}</p>
    @endif
    <x-input-error class="mt-1.5" :messages="$errors->get($name)" />
    <p x-show="errorFor(@js($name))" x-cloak x-text="errorFor(@js($name))" data-upload-error class="mt-1.5 text-sm text-rose-400"></p>
</div>
