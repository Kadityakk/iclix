<div x-show="generalError" x-cloak x-text="generalError" class="rounded-xl bg-rose-500/15 px-4 py-3 text-sm text-rose-300" role="alert"></div>

<div x-show="isUploading" x-cloak class="space-y-2 rounded-xl bg-white/5 p-4" role="status">
    <div class="flex justify-between text-sm">
        <span>Mengunggah file...</span>
        <span class="tabular-nums" x-text="`${progressPercent}%`"></span>
    </div>
    <div class="h-2 overflow-hidden rounded-full bg-white/10">
        <div class="h-full rounded-full bg-gradient-to-r from-brand to-glow transition-[width]" :style="`width: ${progressPercent}%`"></div>
    </div>
</div>
