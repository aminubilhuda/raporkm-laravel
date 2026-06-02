@extends('layouts.tu')

@php
    $isEdit = $pegawai->exists;
@endphp

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-user-plus class="w-7 h-7" />
            {{ $isEdit ? 'Edit Pegawai' : 'Tambah Pegawai' }}
        </h1>
        <p class="mt-1 text-sm text-gray-500">{{ $isEdit ? 'Perbarui data pegawai.' : 'Tambahkan pegawai atau guru baru.' }}</p>
    </div>

    <form method="POST" action="{{ $isEdit ? route('tu.pegawai.update', $pegawai) : route('tu.pegawai.store') }}" class="space-y-6">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Data Diri</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="nama" value="Nama Lengkap" />
                    <x-text-input id="nama" name="nama" :value="old('nama', $pegawai->nama)" class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="jabatan" value="Jabatan" />
                    <select id="jabatan" name="jabatan" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20" required>
                        <option value="2" {{ old('jabatan', $pegawai->jabatan) == 2 ? 'selected' : '' }}>Tata Usaha</option>
                        <option value="3" {{ old('jabatan', $pegawai->jabatan) == 3 ? 'selected' : '' }}>Guru</option>
                        <option value="4" {{ old('jabatan', $pegawai->jabatan) == 4 ? 'selected' : '' }}>Kepala Sekolah</option>
                    </select>
                    <x-input-error :messages="$errors->get('jabatan')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="nip" value="NIP" />
                    <x-text-input id="nip" name="nip" :value="old('nip', $pegawai->nip)" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('nip')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="nuptk" value="NUPTK" />
                    <x-text-input id="nuptk" name="nuptk" :value="old('nuptk', $pegawai->nuptk)" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('nuptk')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="kelamin" value="Jenis Kelamin" />
                    <select id="kelamin" name="kelamin" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                        <option value="">-- Pilih --</option>
                        <option value="1" {{ old('kelamin', $pegawai->kelamin) == 1 ? 'selected' : '' }}>Laki-laki</option>
                        <option value="2" {{ old('kelamin', $pegawai->kelamin) == 2 ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="agama" value="Agama" />
                    <select id="agama" name="agama" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                        <option value="">-- Pilih --</option>
                        <option value="1" {{ old('agama', $pegawai->agama) == 1 ? 'selected' : '' }}>Islam</option>
                        <option value="2" {{ old('agama', $pegawai->agama) == 2 ? 'selected' : '' }}>Kristen</option>
                        <option value="3" {{ old('agama', $pegawai->agama) == 3 ? 'selected' : '' }}>Katolik</option>
                        <option value="4" {{ old('agama', $pegawai->agama) == 4 ? 'selected' : '' }}>Hindu</option>
                        <option value="5" {{ old('agama', $pegawai->agama) == 5 ? 'selected' : '' }}>Buddha</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="kontak" value="Kontak (No. HP)" />
                    <x-text-input id="kontak" name="kontak" :value="old('kontak', $pegawai->kontak)" class="block w-full mt-1" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" :value="old('email', $pegawai->email)" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Akun</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="username" value="Username" />
                    <x-text-input id="username" name="username" :value="old('username', $pegawai->username)" class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('username')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="password" value="Password {{ $isEdit ? '(kosongkan jika tidak diubah)' : '' }}" />
                    <x-text-input id="password" name="password" type="password" class="block w-full mt-1" {{ $isEdit ? '' : 'required' }} />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="id_kepegawaian" value="Status Kepegawaian" />
                    <select id="id_kepegawaian" name="id_kepegawaian" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                        <option value="">-- Pilih --</option>
                        <option value="1" {{ old('id_kepegawaian', $pegawai->id_kepegawaian) == 1 ? 'selected' : '' }}>PNS</option>
                        <option value="2" {{ old('id_kepegawaian', $pegawai->id_kepegawaian) == 2 ? 'selected' : '' }}>PPPK</option>
                        <option value="3" {{ old('id_kepegawaian', $pegawai->id_kepegawaian) == 3 ? 'selected' : '' }}>GTY</option>
                        <option value="4" {{ old('id_kepegawaian', $pegawai->id_kepegawaian) == 4 ? 'selected' : '' }}>GTT</option>
                        <option value="5" {{ old('id_kepegawaian', $pegawai->id_kepegawaian) == 5 ? 'selected' : '' }}>Honorer</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="ijazah" value="Ijazah Tertinggi" />
                    <select id="ijazah" name="ijazah" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                        <option value="">-- Pilih --</option>
                        <option value="7" {{ old('ijazah', $pegawai->ijazah) == 7 ? 'selected' : '' }}>S1/D4</option>
                        <option value="8" {{ old('ijazah', $pegawai->ijazah) == 8 ? 'selected' : '' }}>S2</option>
                        <option value="9" {{ old('ijazah', $pegawai->ijazah) == 9 ? 'selected' : '' }}>S3</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-sky">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Informasi Tambahan</h2>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <x-input-label for="id_tugas_tambahan" value="Tugas Tambahan" />
                    <select id="id_tugas_tambahan" name="id_tugas_tambahan" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                        <option value="">-- Pilih --</option>
                        <option value="1" {{ old('id_tugas_tambahan', $pegawai->id_tugas_tambahan) == 1 ? 'selected' : '' }}>Wali Kelas</option>
                        <option value="2" {{ old('id_tugas_tambahan', $pegawai->id_tugas_tambahan) == 2 ? 'selected' : '' }}>Pembina Eskul</option>
                        <option value="3" {{ old('id_tugas_tambahan', $pegawai->id_tugas_tambahan) == 3 ? 'selected' : '' }}>Pembimbing Prakerin</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="moto" value="Moto" />
                    <textarea id="moto" name="moto" rows="2" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">{{ old('moto', $pegawai->moto) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('tu.pegawai.index') }}" class="btn-secondary inline-flex items-center gap-2">Batal</a>
            <x-primary-button>{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Pegawai' }}</x-primary-button>
        </div>
    </form>
</div>
@endsection