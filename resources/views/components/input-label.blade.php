@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-body/90']) }}>
    {{ $value ?? $slot }}
</label>
