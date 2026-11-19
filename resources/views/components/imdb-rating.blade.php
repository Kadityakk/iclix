@props(['content'])

@if ($content->formattedImdbRating())
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 font-semibold text-amber-400']) }} title="Rating IMDb">
        <x-icon name="star" class="size-4" />{{ $content->formattedImdbRating() }}
    </span>
@endif
