@extends('layouts.tu')

@php $isEdit = $mapel->exists; @endphp

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-book-open class="w-7 h-7" />
            {{ $isEdit ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
        </h1>
        <p class="mt-1 text-sm text-gray-500">{{ $isEdit ? 'Perbarui data mata pelajaran.' : 'Tambahkan mata pelajaran baru.' }}</p>
    </div>

    <form method="POST" action="{{ $isEdit ? route('tu.mapel.update', $mapel) : route('tu.mapel.store') }}" class="space-y-6">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Informasi Mapel</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="nama_mapel" value="Nama Mata Pelajaran" />
                    <x-text-input id="nama_mapel" name="nama_mapel" :value="old('nama_mapel', $mapel->nama_mapel)" class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('nama_mapel')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="kode" value="Kode" />
                    <x-text-input id="kode" name="kode" :value="old('kode', $mapel->kode)" placeholder="MTK" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('kode')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="kelompok_mapel_id" value="Kelompok Mapel" />
                    <select id="kelompok_mapel_id" name="kelompok_mapel_id" class="mt-1 block w-full border-teal-primary/20 rounded-field bg-cream focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20" required>
                        <option value="">-- Pilih --</option>
                        @foreach($kelompok as $k)
                            <option value="{{ $k->id }}" {{ old('kelompok_mapel_id', $mapel->kelompok_mapel_id) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('kelompok_mapel_id')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="kkm" value="KKM" />
                    <x-text-input id="kkm" name="kkm" type="number" :value="old('kkm', $mapel->kkm ?: 75)" min="0" max="100" class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('kkm')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="urutan" value="Urutan" />
                    <x-text-input id="urutan" name="urutan" type="number" :value="old('urutan', $mapel->urutan)" min="0" placeholder="1" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('urutan')" class="mt-1" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('tu.mapel.index') }}" class="btn-secondary inline-flex items-center gap-2">Batal</a>
            <x-primary-button>{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Mapel' }}</x-primary-button>
        </div>
    </form>
</div>
@endsection
