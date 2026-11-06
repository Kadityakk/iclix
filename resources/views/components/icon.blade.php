@props(['name', 'filled' => false])

@php
$paths = [
    'home' => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 9.5V20h13V9.5"/><path d="M10 20v-6h4v6"/>',
    'film' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 4v16M16 4v16M3 9h5M3 15h5M16 9h5M16 15h5"/>',
    'tv' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="m8 3 4 4 4-4"/>',
    'flame' => '<path d="M12 3s5 4.5 5 10a5 5 0 0 1-10 0c0-1.8.7-3.2 1.7-4.3.3 1.6 1 2.5 2 3C10.500 9 10.500 6 12 3Z"/>',
    'bookmark' => '<path d="M6 4h12v17l-6-4-6 4V4Z"/>',
    'tag' => '<path d="M3 12V4h8l10 10-8 8L3 12Z"/><circle cx="7.500" cy="8.500" r="1.200"/>',
    'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
    'sliders' => '<path d="M4 7h10M18 7h2M4 17h2M10 17h10"/><circle cx="16" cy="7" r="2"/><circle cx="8" cy="17" r="2"/>',
    'play' => '<path d="M7 4.500v15a.6.6 0 0 0 .9.5l12-7.500a.6.6 0 0 0 0-1l-12-7.500a.6.6 0 0 0-.9.5Z" fill="currentColor"/>',
    'pause' => '<rect x="6" y="4" width="4" height="16" rx="1" fill="currentColor"/><rect x="14" y="4" width="4" height="16" rx="1" fill="currentColor"/>',
    'heart' => '<path d="M12 20s-8-4.800-8-10.500A4.500 4.500 0 0 1 12 7a4.500 4.500 0 0 1 8 2.500C20 15.200 12 20 12 20Z"/>',
    'dislike' => '<path d="M17 3v11M7 3h10v11l-4 7a2 2 0 0 1-2-2v-4H5.500a2 2 0 0 1-2-2.400l1.200-7A2 2 0 0 1 6.700 3H7Z"/>',
    'like' => '<path d="M7 21V10M17 21H7V10l4-7a2 2 0 0 1 2 2v4h5.500a2 2 0 0 1 2 2.400l-1.200 7a2 2 0 0 1-2 1.600H17Z"/>',
    'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 7.500v.5"/>',
    'close' => '<path d="m6 6 12 12M18 6 6 18"/>',
    'left' => '<path d="m15 5-7 7 7 7"/>',
    'right' => '<path d="m9 5 7 7-7 7"/>',
    'check' => '<path d="m5 12.500 4.500 4.500L19 7.500"/>',
    'plus' => '<path d="M12 5v14M5 12h14"/>',
    'user' => '<circle cx="12" cy="8" r="4"/><path d="M4.500 20.500c1-4 4-6 7.500-6s6.500 2 7.500 6"/>',
    'logout' => '<path d="M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4"/><path d="M10 8l-4 4 4 4M6 12h10"/>',
    'pencil' => '<path d="M4 20h4L20 8l-4-4L4 16v4Z"/>',
    'trash' => '<path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/>',
    'volume' => '<path d="M4 9.500v5h4l5 4v-13l-5 4H4Z"/><path d="M16.500 9a4 4 0 0 1 0 6M19 6.500a8 8 0 0 1 0 11"/>',
    'mute' => '<path d="M4 9.500v5h4l5 4v-13l-5 4H4Z"/><path d="m16.500 9.500 5 5M21.500 9.500l-5 5"/>',
    'fullscreen' => '<path d="M4 9V4h5M15 4h5v5M20 15v5h-5M9 20H4v-5"/>',
    'back10' => '<path d="M4 12a8 8 0 1 0 2.500-5.800L4 8.500"/><path d="M4 4v4.500h4.500"/>',
    'forward10' => '<path d="M20 12a8 8 0 1 1-2.500-5.800L20 8.500"/><path d="M20 4v4.500h-4.500"/>',
    'chart' => '<path d="M4 20V4M4 20h16M8 16v-5M13 16V8M18 16v-3"/>',
    'card' => '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10.500h18"/>',
    'users' => '<circle cx="9" cy="8" r="3.500"/><path d="M3 20c.7-3.500 3-5.500 6-5.500s5.300 2 6 5.500"/><path d="M16 5a3.500 3.500 0 0 1 0 6.500M18 14.800c1.500.8 2.600 2.500 3 5.200"/>',
];
@endphp

<svg {{ $attributes->merge(['class' => 'size-5']) }} viewBox="0 0 24 24" fill="{{ $filled ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.800" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] ?? '' !!}</svg>
