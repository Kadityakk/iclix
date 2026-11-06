@props(['profile'])

<span {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-full bg-gradient-to-br font-bold text-white '.$profile->avatarGradient()]) }} aria-hidden="true">
    {{ mb_strtoupper(mb_substr($profile->name, 0, 1)) }}
</span>
