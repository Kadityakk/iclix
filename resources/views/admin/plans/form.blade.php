<x-admin-layout>
    <x-admin.page-header :title="$plan->exists ? 'Ubah paket' : 'Tambah paket'" :back-url="route('admin.plans.index')" />

    <form method="POST" action="{{ $plan->exists ? route('admin.plans.update', $plan) : route('admin.plans.store') }}"
          x-data="uploadForm()" class="grid max-w-2xl gap-5 rounded-2xl border border-white/10 bg-card p-6 sm:grid-cols-2">
        @csrf
        @if ($plan->exists)
            @method('PUT')
        @endif

        <x-admin.field name="name" label="Nama">
            <x-text-input id="name" name="name" :value="old('name', $plan->name)" required maxlength="255" />
        </x-admin.field>
        <x-admin.field name="price" label="Harga (Rp)">
            <x-text-input id="price" type="number" name="price" min="0" :value="old('price', $plan->price)" required />
        </x-admin.field>
        <x-admin.field name="quality" label="Kualitas">
            <x-admin.select id="quality" name="quality" required>
                @foreach (\App\Models\Plan::QUALITIES as $quality)
                    <option value="{{ $quality }}" @selected(old('quality', $plan->quality) === $quality)>{{ $quality }}</option>
                @endforeach
            </x-admin.select>
        </x-admin.field>
        <x-admin.field name="max_screens" label="Jumlah layar">
            <x-text-input id="max_screens" type="number" name="max_screens" min="1" :value="old('max_screens', $plan->max_screens)" required />
        </x-admin.field>
        <x-admin.field name="duration_days" label="Durasi (hari)">
            <x-text-input id="duration_days" type="number" name="duration_days" min="1" :value="old('duration_days', $plan->duration_days)" required />
        </x-admin.field>

        <div class="sm:col-span-2"><x-primary-button>Simpan</x-primary-button></div>
    </form>
</x-admin-layout>
