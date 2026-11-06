<x-admin-layout>
    <x-admin.page-header :title="$episode->exists ? 'Ubah episode' : 'Tambah episode'" :back-url="route('admin.seasons.episodes.index', $season)" />

    <form method="POST" enctype="multipart/form-data" action="{{ $episode->exists ? route('admin.episodes.update', $episode) : route('admin.seasons.episodes.store', $season) }}"
          x-data="uploadForm()" @submit="submit($event)" class="max-w-2xl space-y-5 rounded-2xl border border-white/10 bg-card p-6">
        @csrf
        @if ($episode->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.field name="episode_number" label="Nomor episode">
                <x-text-input id="episode_number" type="number" name="episode_number" min="1" :value="old('episode_number', $episode->episode_number)" required />
            </x-admin.field>
            <x-admin.field name="duration_min" label="Durasi (menit)">
                <x-text-input id="duration_min" type="number" name="duration_min" min="1" :value="old('duration_min', $episode->duration_min)" required />
            </x-admin.field>
        </div>

        <x-admin.field name="title" label="Judul">
            <x-text-input id="title" name="title" :value="old('title', $episode->title)" required maxlength="255" />
        </x-admin.field>

        <x-admin.field name="synopsis" label="Sinopsis">
            <textarea id="synopsis" name="synopsis" rows="3" maxlength="2000"
                      class="w-full rounded-xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm text-body focus:border-brand-hover focus:ring-2 focus:ring-brand/40 focus:outline-none">{{ old('synopsis', $episode->synopsis) }}</textarea>
        </x-admin.field>

        <x-admin.field name="thumbnail" label="Thumbnail" hint="JPG, PNG, atau WebP, maks. 2 MB.">
            <x-admin.file-input id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/webp" />
        </x-admin.field>

        <x-admin.field name="video" label="Video" :hint="'MP4, maks. 500 MB.'.($episode->video_path ? ' Kosongkan untuk memakai video yang ada.' : '')">
            <x-admin.file-input id="video" name="video" accept="video/mp4" />
        </x-admin.field>

        <x-admin.upload-progress />

        <x-primary-button x-bind:disabled="isUploading">Simpan</x-primary-button>
    </form>
</x-admin-layout>
