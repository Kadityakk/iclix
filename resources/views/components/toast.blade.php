@php
$flashMessage = session('error') ?? (is_string(session('status')) && ! in_array(session('status'), ['profile-updated', 'password-updated', 'verification-link-sent'], true) ? session('status') : null);
@endphp

<div x-data @if ($flashMessage) x-init="$store.toast.show(@js($flashMessage))" @endif
     x-show="$store.toast.isVisible" x-cloak x-transition.opacity role="status" aria-live="polite"
     class="fixed right-4 bottom-20 z-[70] max-w-xs rounded-xl border border-white/10 bg-card px-4 py-3 text-sm font-medium shadow-2xl md:right-6 md:bottom-6">
    <span x-text="$store.toast.message"></span>
</div>
