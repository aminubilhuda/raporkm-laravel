@extends('layouts.tu')

@php $isEdit = $siswa->exists; @endphp

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-user-plus class="w-7 h-7" />
            {{ $isEdit ? 'Edit Siswa' : 'Tambah Siswa' }}
        </h1>
        <p class="mt-1 text-sm text-gray-500">{{ $isEdit ? 'Perbarui data siswa.' : 'Tambahkan siswa baru.' }}</p>
    </div>

    <form method="POST" action="{{ $isEdit ? route('tu.kesiswaan.update', $siswa) : route('tu.kesiswaan.store') }}">
        @csrf @if($isEdit) @method('PUT') @endif

        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary space-y-4">
            <h2 class="text-lg font-extrabold text-teal-primary-dark">Data Pribadi</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="nama_siswa" value="Nama Lengkap" />
                    <x-text-input id="nama_siswa" name="nama_siswa" :value="old('nama_siswa', $siswa->nama_siswa)" class="block w-full mt-1" required />
                </div>
                <div>
                    <x-input-label for="nisn" value="NISN" />
                    <x-text-input id="nisn" name="nisn" :value="old('nisn', $siswa->nisn)" class="block w-full mt-1" required />
                </div>
                <div>
                    <x-input-label for="nis" value="NIS" />
                    <x-text-input id="nis" name="nis" :value="old('nis', $siswa->nis)" class="block w-full mt-1" required />
                </div>
                <div>
                    <x-input-label for="nik_pd" value="NIK PD" />
                    <x-text-input id="nik_pd" name="nik_pd" :value="old('nik_pd', $siswa->nik_pd)" class="block w-full mt-1" />
                </div>
                <div>
                    <x-input-label for="tempat_lahir" value="Tempat Lahir" />
                    <x-text-input id="tempat_lahir" name="tempat_lahir" :value="old('tempat_lahir', $siswa->tempat_lahir)" class="block w-full mt-1" />
                </div>
                <div>
                    <x-input-label for="tanggal_lahir" value="Tanggal Lahir" />
                    <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date" :value="old('tanggal_lahir', $siswa->tanggal_lahir?->format('Y-m-d'))" class="block w-full mt-1" />
                </div>
                <div>
                    <x-input-label for="kelamin" value="Jenis Kelamin" />
                    <select id="kelamin" name="kelamin" class="mt-1 block w-full border-teal-primary/20 rounded-card">
                        <option value="">-- Pilih --</option>
                        <option value="1" {{ old('kelamin', $siswa->kelamin) == 1 ? 'selected' : '' }}>Laki-laki</option>
                        <option value="2" {{ old('kelamin', $siswa->kelamin) == 2 ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="agama" value="Agama" />
                    <select id="agama" name="agama" class="mt-1 block w-full border-teal-primary/20 rounded-card">
                        <option value="">-- Pilih --</option>
                        @foreach([1=>'Islam',2=>'Kristen',3=>'Katolik',4=>'Hindu',5=>'Buddha'] as $id => $nama)
                            <option value="{{ $id }}" {{ old('agama', $siswa->agama) == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="kontak_siswa" value="Kontak" />
                    <x-text-input id="kontak_siswa" name="kontak_siswa" :value="old('kontak_siswa', $siswa->kontak_siswa)" class="block w-full mt-1" />
                </div>
                <div class="md:col-span-3">
                    <x-input-label for="alamat" value="Alamat" />
                    <textarea id="alamat" name="alamat" rows="2" class="mt-1 block w-full border-teal-primary/20 rounded-card">{{ old('alamat', $siswa->alamat) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold space-y-4 mt-6">
            <h2 class="text-lg font-extrabold text-teal-primary-dark">Data Orang Tua / Wali</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-gray-200 rounded-card p-4 space-y-3">
                    <h3 class="text-sm font-extrabold text-gray-500 uppercase tracking-wider">Ayah</h3>
                    <x-input-label for="nama_ayah" value="Nama Ayah" />
                    <x-text-input id="nama_ayah" name="nama_ayah" :value="old('nama_ayah', $siswa->nama_ayah)" class="block w-full mt-1" />
                    <x-input-label for="nik_ayah" value="NIK Ayah" />
                    <x-text-input id="nik_ayah" name="nik_ayah" :value="old('nik_ayah', $siswa->nik_ayah)" class="block w-full mt-1" />
                    <x-input-label for="pekerjaan_ayah" value="Pekerjaan" />
                    <x-text-input id="pekerjaan_ayah" name="pekerjaan_ayah" :value="old('pekerjaan_ayah', $siswa->pekerjaan_ayah)" class="block w-full mt-1" />
                    <x-input-label for="kontak_ayah" value="Kontak" />
                    <x-text-input id="kontak_ayah" name="kontak_ayah" :value="old('kontak_ayah', $siswa->kontak_ayah)" class="block w-full mt-1" />
                </div>
                <div class="border border-gray-200 rounded-card p-4 space-y-3">
                    <h3 class="text-sm font-extrabold text-gray-500 uppercase tracking-wider">Ibu</h3>
                    <x-input-label for="nama_ibu" value="Nama Ibu" />
                    <x-text-input id="nama_ibu" name="nama_ibu" :value="old('nama_ibu', $siswa->nama_ibu)" class="block w-full mt-1" />
                    <x-input-label for="nik_ibu" value="NIK Ibu" />
                    <x-text-input id="nik_ibu" name="nik_ibu" :value="old('nik_ibu', $siswa->nik_ibu)" class="block w-full mt-1" />
                    <x-input-label for="pekerjaan_ibu" value="Pekerjaan" />
                    <x-text-input id="pekerjaan_ibu" name="pekerjaan_ibu" :value="old('pekerjaan_ibu', $siswa->pekerjaan_ibu)" class="block w-full mt-1" />
                    <x-input-label for="kontak_ibu" value="Kontak" />
                    <x-text-input id="kontak_ibu" name="kontak_ibu" :value="old('kontak_ibu', $siswa->kontak_ibu)" class="block w-full mt-1" />
                </div>
            </div>
            <div class="border border-gray-200 rounded-card p-4 space-y-3">
                <h3 class="text-sm font-extrabold text-gray-500 uppercase tracking-wider">Wali (Opsional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="nama_wali" value="Nama Wali" />
                        <x-text-input id="nama_wali" name="nama_wali" :value="old('nama_wali', $siswa->nama_wali)" class="block w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label for="pekerjaan_wali" value="Pekerjaan" />
                        <x-text-input id="pekerjaan_wali" name="pekerjaan_wali" :value="old('pekerjaan_wali', $siswa->pekerjaan_wali)" class="block w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label for="kontak_wali" value="Kontak" />
                        <x-text-input id="kontak_wali" name="kontak_wali" :value="old('kontak_wali', $siswa->kontak_wali)" class="block w-full mt-1" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('tu.kesiswaan.index') }}" class="btn-secondary inline-flex items-center gap-2">Batal</a>
            <x-primary-button>{{ $isEdit ? 'Simpan' : 'Tambah Siswa' }}</x-primary-button>
        </div>
    </form>
</div>
@endsection