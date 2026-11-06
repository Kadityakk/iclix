<x-admin-layout>
    <x-admin.page-header :title="($season->exists ? 'Ubah musim: ' : 'Tambah musim: ').$content->title" :back-url="route('admin.contents.seasons.index', $content)" />

    <form method="POST" action="{{ $season->exists ? route('admin.seasons.update', $season) : route('admin.contents.seasons.store', $content) }}"
          x-data="uploadForm()" class="max-w-xl space-y-5 rounded-2xl border border-white/10 bg-card p-6">
        @csrf
        @if ($season->exists)
            @method('PUT')
        @endif

        <x-admin.field name="season_number" label="Nomor musim">
            <x-text-input id="season_number" type="number" name="season_number" min="1" :value="old('season_number', $season->season_number)" required />
        </x-admin.field>

        <x-admin.field name="title" label="Judul (opsional)">
            <x-text-input id="title" name="title" :value="old('title', $season->title)" maxlength="255" />
        </x-admin.field>

        <x-primary-button>Simpan</x-primary-button>
    </form>
</x-admin-layout>
