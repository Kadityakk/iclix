<x-guest-layout>
    <h1 class="text-2xl font-bold">{{ $profile->exists ? 'Ubah profil' : 'Tambah profil' }}</h1>

    <form method="POST" action="{{ $profile->exists ? route('profiles.update', $profile) : route('profiles.store') }}" class="mt-6 space-y-5">
        @csrf
        @if ($profile->exists)
            @method('PUT')
        @endif

        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" name="name" class="mt-1" :value="old('name', $profile->name)" required autofocus maxlength="50" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <fieldset>
            <legend class="text-sm font-medium text-body/90">Avatar</legend>
            <div class="mt-2 flex flex-wrap gap-3">
                @foreach (\App\Models\Profile::AVATARS as $avatar => $gradient)
                    <label class="cursor-pointer">
                        <input type="radio" name="avatar" value="{{ $avatar }}" class="peer sr-only" @checked(old('avatar', $profile->avatar) === $avatar)>
                        <span class="block size-12 rounded-full bg-gradient-to-br {{ $gradient }} ring-white ring-offset-2 ring-offset-card transition peer-checked:ring-2 peer-focus-visible:ring-2"></span>
                        <span class="sr-only">{{ $avatar }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </fieldset>

        <label class="flex items-start gap-3 rounded-xl bg-white/5 p-3">
            <input type="hidden" name="is_kids" value="0">
            <input type="checkbox" name="is_kids" value="1" class="mt-1 size-4 accent-brand" @checked(old('is_kids', $profile->is_kids))>
            <span class="text-sm">
                <span class="font-medium">Profil anak</span>
                <span class="block text-muted">Hanya menampilkan konten SU dan 7+.</span>
            </span>
        </label>

        <div class="flex items-center gap-3">
            <x-primary-button>Simpan</x-primary-button>
            <a href="{{ route('profiles.index') }}" class="text-sm text-muted underline">Batal</a>
        </div>
    </form>
</x-guest-layout>
