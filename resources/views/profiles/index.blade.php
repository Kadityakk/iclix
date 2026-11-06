<x-guest-wide title="Pilih profil">
    <div class="mx-auto flex max-w-4xl flex-col items-center pt-8 text-center" x-data="{ isManaging: false }">
        <h1 class="text-3xl font-bold md:text-5xl">Siapa yang menonton?</h1>

        <div class="mt-12 flex flex-wrap justify-center gap-6 sm:gap-8">
            @foreach ($profiles as $profile)
                <div class="relative w-28 sm:w-36" x-reveal>
                    <form method="POST" action="{{ route('profiles.select', $profile) }}">
                        @csrf
                        <button class="group w-full space-y-3" aria-label="Pilih profil {{ $profile->name }}">
                            <x-avatar :profile="$profile" class="aspect-square w-full text-4xl ring-white transition group-hover:scale-105 group-hover:ring-4 group-focus-visible:ring-4 sm:text-5xl" />
                            <span class="block truncate font-medium text-muted transition group-hover:text-white">{{ $profile->name }}</span>
                        </button>
                    </form>

                    @if ($profile->is_kids)
                        <span class="absolute top-0 right-0 rounded-full bg-amber-400 px-2 py-0.5 text-xs font-bold text-black">Anak</span>
                    @endif

                    <div x-show="isManaging" x-cloak x-transition.opacity class="mt-2 flex justify-center gap-2">
                        <a href="{{ route('profiles.edit', $profile) }}" class="flex size-9 items-center justify-center rounded-full bg-white/10 hover:bg-white/20" aria-label="Ubah profil {{ $profile->name }}">
                            <x-icon name="pencil" class="size-4" />
                        </a>
                        <form method="POST" action="{{ route('profiles.destroy', $profile) }}" @submit="confirm('Hapus profil ini beserta riwayatnya?') || $event.preventDefault()">
                            @csrf
                            @method('DELETE')
                            <button class="flex size-9 items-center justify-center rounded-full bg-white/10 hover:bg-rose-600" aria-label="Hapus profil {{ $profile->name }}">
                                <x-icon name="trash" class="size-4" />
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            @if ($canAddProfile)
                <a href="{{ route('profiles.create') }}" class="group w-28 space-y-3 sm:w-36">
                    <span class="flex aspect-square w-full items-center justify-center rounded-full border-2 border-dashed border-white/30 text-muted transition group-hover:border-white group-hover:text-white">
                        <x-icon name="plus" class="size-10" />
                    </span>
                    <span class="block font-medium text-muted group-hover:text-white">Tambah profil</span>
                </a>
            @endif
        </div>

        <button type="button" @click="isManaging = ! isManaging" class="mt-12 rounded-full border border-white/30 px-6 py-2.5 text-sm font-medium transition hover:bg-white/10"
                x-text="isManaging ? 'Selesai' : 'Kelola profil'"></button>
    </div>
</x-guest-wide>
