@props(['profile'])

<span {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-full font-bold text-white '.$profile->avatarColor()]) }} aria-hidden="true">
    {{ mb_strtoupper(mb_substr($profile->name, 0, 1)) }}
</span>
