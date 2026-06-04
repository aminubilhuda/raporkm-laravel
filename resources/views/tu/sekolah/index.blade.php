@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-building-office-2 class="w-7 h-7" />
            Profil Sekolah
        </h1>
        <p class="mt-1 text-sm text-gray-500">Kelola informasi dan identitas sekolah.</p>
    </div>

    <form method="POST" action="{{ route('tu.sekolah.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        {{-- Logo Sekolah --}}
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Logo Sekolah</h2>
            <div class="flex flex-col sm:flex-row gap-6 items-start">
                <div class="flex-shrink-0">
                    <div class="w-36 h-36 rounded-card border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden bg-gray-50">
                        @if($sekolah->logo)
                            <img id="logo-preview" src="{{ asset('storage/'.$sekolah->logo) }}" alt="Logo" class="w-full h-full object-contain">
                        @else
                            <img id="logo-preview" src="" alt="" class="w-full h-full object-contain hidden">
                            <div id="logo-placeholder" class="text-center">
                                <x-heroicon-o-photo class="w-10 h-10 text-gray-300 mx-auto" />
                                <p class="text-xs text-gray-400 mt-1">Belum ada logo</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    <div>
                        <x-input-label for="logo" value="Upload Logo Baru" />
                        <input type="file" id="logo" name="logo" accept="image/jpeg,image/png"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-teal-primary/10 file:text-teal-primary hover:file:bg-teal-primary/20"
                            onchange="previewLogo(this)">
                        <x-input-error :messages="$errors->get('logo')" class="mt-1" />
                        <p class="text-xs text-gray-400 mt-1">Format: JPG/PNG, maks 5MB. Favicon akan otomatis dibuat dari logo.</p>
                    </div>
                    @if($sekolah->logo)
                        <label class="flex items-center gap-2 text-sm text-coral cursor-pointer">
                            <input type="checkbox" name="hapus_logo" value="1" class="rounded border-coral text-coral focus:ring-coral">
                            Hapus logo saat ini
                        </label>
                        <p class="text-xs text-gray-400">File: {{ basename($sekolah->logo) }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Identitas Sekolah --}}
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Identitas Sekolah</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="npsn" value="NPSN" />
                    <x-text-input id="npsn" name="npsn" :value="old('npsn', $sekolah->npsn)" class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('npsn')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="nama_sekolah" value="Nama Sekolah" />
                    <x-text-input id="nama_sekolah" name="nama_sekolah" :value="old('nama_sekolah', $sekolah->nama_sekolah)" class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('nama_sekolah')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" :value="old('email', $sekolah->email)" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="kontak" value="Kontak" />
                    <x-text-input id="kontak" name="kontak" :value="old('kontak', $sekolah->kontak)" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('kontak')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="website" value="Website" />
                    <x-text-input id="website" name="website" type="url" :value="old('website', $sekolah->website)" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('website')" class="mt-1" />
                </div>
            </div>
        </div>

        {{-- Alamat --}}
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Alamat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-input-label for="alamat" value="Alamat Lengkap" />
                    <textarea id="alamat" name="alamat" rows="2" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">{{ old('alamat', $sekolah->alamat) }}</textarea>
                    <x-input-error :messages="$errors->get('alamat')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="desa" value="Desa/Kelurahan" />
                    <x-text-input id="desa" name="desa" :value="old('desa', $sekolah->desa)" class="block w-full mt-1" />
                </div>
                <div>
                    <x-input-label for="kecamatan" value="Kecamatan" />
                    <x-text-input id="kecamatan" name="kecamatan" :value="old('kecamatan', $sekolah->kecamatan)" class="block w-full mt-1" />
                </div>
                <div>
                    <x-input-label for="kabupaten" value="Kabupaten" />
                    <x-text-input id="kabupaten" name="kabupaten" :value="old('kabupaten', $sekolah->kabupaten)" class="block w-full mt-1" />
                </div>
                <div>
                    <x-input-label for="provinsi" value="Provinsi" />
                    <x-text-input id="provinsi" name="provinsi" :value="old('provinsi', $sekolah->provinsi)" class="block w-full mt-1" />
                </div>
            </div>
        </div>

        {{-- Visi & Misi --}}
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-sky">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Visi & Misi</h2>
            <div class="space-y-4">
                <div>
                    <x-input-label for="visi" value="Visi" />
                    <textarea id="visi" name="visi" rows="3" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">{{ old('visi', $sekolah->visi) }}</textarea>
                    <x-input-error :messages="$errors->get('visi')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="misi" value="Misi" />
                    <textarea id="misi" name="misi" rows="5" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">{{ old('misi', $sekolah->misi) }}</textarea>
                    <x-input-error :messages="$errors->get('misi')" class="mt-1" />
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <x-primary-button>Simpan Perubahan</x-primary-button>
        </div>
    </form>
</div>

<script>
    function previewLogo(input) {
        const preview = document.getElementById('logo-preview');
        const placeholder = document.getElementById('logo-placeholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
