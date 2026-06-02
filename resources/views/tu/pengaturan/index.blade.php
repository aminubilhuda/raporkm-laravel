@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-cog-6-tooth class="w-7 h-7" />
            Pengaturan
        </h1>
        <p class="mt-1 text-sm text-gray-500">Atur tahun pelajaran, semester aktif, dan tanggal pembagian rapor.</p>
    </div>

    <form method="POST" action="{{ route('tu.pengaturan.update') }}" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tahun Pelajaran & Semester Aktif</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="tahun_pelajaran_id" value="Tahun Pelajaran Aktif" />
                    <select id="tahun_pelajaran_id" name="tahun_pelajaran_id" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                        @foreach($tahunPelajarans as $tp)
                            <option value="{{ $tp->id }}" {{ old('tahun_pelajaran_id', $sekolah->tahun_aktif) == $tp->id ? 'selected' : '' }}>
                                {{ $tp->tahun }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('tahun_pelajaran_id')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="semester_id" value="Semester Aktif" />
                    <select id="semester_id" name="semester_id" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                        @foreach($semesters as $s)
                            <option value="{{ $s->id }}" {{ old('semester_id', $sekolah->semester_aktif) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('semester_id')" class="mt-1" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Pembagian Rapor</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="tanggal_mid" value="Tanggal Rapor Mid Semester" />
                    <x-text-input id="tanggal_mid" name="tanggal_mid" type="date" :value="old('tanggal_mid', optional($pembagian)->tanggal_mid?->format('Y-m-d'))" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('tanggal_mid')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="tanggal_semester" value="Tanggal Rapor Semester" />
                    <x-text-input id="tanggal_semester" name="tanggal_semester" type="date" :value="old('tanggal_semester', optional($pembagian)->tanggal_semester?->format('Y-m-d'))" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('tanggal_semester')" class="mt-1" />
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <x-primary-button>Simpan Pengaturan</x-primary-button>
        </div>
    </form>
</div>
@endsection