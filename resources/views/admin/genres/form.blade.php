<x-admin-layout>
    <x-admin.page-header :title="$genre->exists ? 'Ubah genre' : 'Tambah genre'" :back-url="route('admin.genres.index')" />

    <form method="POST" action="{{ $genre->exists ? route('admin.genres.update', $genre) : route('admin.genres.store') }}"
          x-data="uploadForm()" class="max-w-xl space-y-5 rounded-2xl border border-white/10 bg-card p-6">
        @csrf
        @if ($genre->exists)
            @method('PUT')
        @endif

        <x-admin.field name="name" label="Nama">
            <x-text-input id="name" name="name" :value="old('name', $genre->name)" required maxlength="255" />
        </x-admin.field>

        <x-primary-button>Simpan</x-primary-button>
    </form>
</x-admin-layout>
