@php
$type = old('type', $content->type?->value ?? 'movie');
@endphp
<x-admin-layout>
    <x-admin.page-header :title="$content->exists ? 'Ubah konten' : 'Tambah konten'" :back-url="route('admin.contents.index')" />

    <form method="POST" enctype="multipart/form-data" action="{{ $content->exists ? route('admin.contents.update', $content) : route('admin.contents.store') }}"
          x-data="{ ...uploadForm(), type: @js($type) }" @submit="submit($event)" class="max-w-4xl space-y-6">
        @csrf
        @if ($content->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 rounded-2xl border border-white/10 bg-card p-6 md:grid-cols-2">
            <x-admin.field name="type" label="Jenis">
                <x-admin.select id="type" name="type" x-model="type" :disabled="$content->exists">
                    @foreach (\App\Enums\ContentType::cases() as $contentType)
                        <option value="{{ $contentType->value }}">{{ $contentType->label() }}</option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>

            <x-admin.field name="age_rating" label="Rating usia">
                <x-admin.select id="age_rating" name="age_rating" required>
                    @foreach (\App\Enums\AgeRating::cases() as $ageRating)
                        <option value="{{ $ageRating->value }}" @selected(old('age_rating', $content->age_rating?->value) === $ageRating->value)>{{ $ageRating->value }}</option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>

            <x-admin.field name="title" label="Judul">
                <x-text-input id="title" name="title" :value="old('title', $content->title)" required maxlength="255" />
            </x-admin.field>

            <x-admin.field name="slug" label="Slug" hint="Kosongkan agar dibuat otomatis dari judul.">
                <x-text-input id="slug" name="slug" :value="old('slug', $content->slug)" maxlength="255" />
            </x-admin.field>

            <x-admin.field name="tagline" label="Tagline" class="md:col-span-2">
                <x-text-input id="tagline" name="tagline" :value="old('tagline', $content->tagline)" maxlength="255" />
            </x-admin.field>

            <x-admin.field name="synopsis" label="Sinopsis" class="md:col-span-2">
                <textarea id="synopsis" name="synopsis" rows="4" required maxlength="5000"
                          class="w-full rounded-xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm text-body focus:border-brand-hover focus:ring-2 focus:ring-brand/40 focus:outline-none">{{ old('synopsis', $content->synopsis) }}</textarea>
            </x-admin.field>

            <x-admin.field name="release_year" label="Tahun rilis">
                <x-text-input id="release_year" type="number" name="release_year" :value="old('release_year', $content->release_year ?? now()->year)" required />
            </x-admin.field>

            <x-admin.field name="release_date" label="Tanggal rilis">
                <x-text-input id="release_date" type="date" name="release_date" :value="old('release_date', $content->release_date?->format('Y-m-d'))" />
            </x-admin.field>

            <div x-show="type === 'movie'">
                <x-admin.field name="duration_min" label="Durasi (menit)">
                    <x-text-input id="duration_min" type="number" name="duration_min" min="1" :value="old('duration_min', $content->duration_min)" x-bind:required="type === 'movie'" />
                </x-admin.field>
            </div>

            <x-admin.field name="trailer_url" label="URL trailer YouTube">
                <x-text-input id="trailer_url" type="url" name="trailer_url" placeholder="https://www.youtube.com/watch?v=..." :value="old('trailer_url', $content->trailer_url)" />
            </x-admin.field>

            <x-admin.field name="cast_members" label="Pemeran" hint="Pisahkan nama dengan koma." class="md:col-span-2">
                <x-text-input id="cast_members" name="cast_members" :value="old('cast_members', implode(', ', $content->cast_members ?? []))" />
            </x-admin.field>

            <fieldset class="md:col-span-2">
                <legend class="text-sm font-medium text-body/90">Genre</legend>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($genres as $genre)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="genres[]" value="{{ $genre->id }}" class="peer sr-only" @checked(in_array($genre->id, old('genres', $selectedGenreIds)))>
                            <span class="block rounded-full border border-white/15 px-3 py-1.5 text-sm transition peer-checked:border-brand peer-checked:bg-brand peer-focus-visible:ring-2 hover:bg-white/10">{{ $genre->name }}</span>
                        </label>
                    @endforeach
                </div>
                <x-input-error class="mt-1.5" :messages="$errors->get('genres')" />
                <p x-show="errorFor('genres')" x-cloak x-text="errorFor('genres')" data-upload-error class="mt-1.5 text-sm text-rose-400"></p>
            </fieldset>

            <label class="flex items-center gap-3 md:col-span-2">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" class="size-4 accent-brand" @checked(old('is_featured', $content->is_featured))>
                <span class="text-sm">Tampilkan di banner hero (maks. {{ \App\Models\Content::MAX_FEATURED }} terbaru)</span>
            </label>
        </div>

        <div class="grid gap-5 rounded-2xl border border-white/10 bg-card p-6 md:grid-cols-2">
            <x-admin.field name="poster" label="Poster" hint="Rasio 2:3. JPG, PNG, atau WebP, maks. 2 MB.">
                <x-admin.file-input id="poster" name="poster" accept="image/jpeg,image/png,image/webp" />
            </x-admin.field>

            <x-admin.field name="backdrop" label="Backdrop" hint="Minimal 1920×1080, subjek di kanan. JPG, PNG, atau WebP, maks. 2 MB.">
                <x-admin.file-input id="backdrop" name="backdrop" accept="image/jpeg,image/png,image/webp" />
            </x-admin.field>

            <div x-show="type === 'movie'" class="md:col-span-2">
                <x-admin.field name="video" label="Video" :hint="'MP4, maks. 500 MB.'.($content->video_path ? ' Kosongkan untuk memakai video yang ada.' : '')">
                    <x-admin.file-input id="video" name="video" accept="video/mp4" />
                </x-admin.field>
            </div>
        </div>

        <x-admin.upload-progress />

        <x-primary-button x-bind:disabled="isUploading">Simpan</x-primary-button>
    </form>
</x-admin-layout>
