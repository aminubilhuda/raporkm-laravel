@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-queue-list class="w-7 h-7" />
            Mapel Siswa
        </h1>
        <p class="mt-1 text-sm text-gray-500">Kelola mata pelajaran yang diambil setiap siswa.</p>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <div>
                <x-input-label for="kelas_id_filter" value="Filter Kelas" />
                <form method="GET">
                    <select id="kelas_id_filter" name="kelas_id" onchange="this.form.submit()" class="mt-1 block w-full border-teal-primary/20 rounded-card">
                        <option value="">Semua Kelas</option>
                        @foreach($mapelKelass->pluck('kelas')->unique('id') as $k)<option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>@endforeach
                    </select>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('tu.mapel-siswa.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end border-t border-gray-200 pt-4">
            @csrf
            <div>
                <x-input-label for="mapel_kelas_id" value="Mapel-Kelas" />
                <select id="mapel_kelas_id" name="mapel_kelas_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                    <option value="">-- Pilih --</option>
                    @foreach($mapelKelass as $mk)<option value="{{ $mk->id }}">{{ $mk->mapel->nama_mapel ?? '' }} - {{ $mk->kelas->nama_kelas ?? '' }}</option>@endforeach
                </select>
            </div>
            <div>
                <x-input-label for="siswa_id" value="Siswa" />
                <select id="siswa_id" name="siswa_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                    <option value="">-- Pilih --</option>
                    @foreach($siswas as $s)<option value="{{ $s->id }}">{{ $s->nama_siswa }}</option>@endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">
                <x-heroicon-o-plus-circle class="w-5 h-5" /> Tambahkan
            </button>
        </form>
    </div>

    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Siswa</th><th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($assignments as $a)<tr class="hover:bg-teal-bg/30"><td class="px-4 py-3 font-bold text-gray-800">{{ $a->siswa->nama_siswa ?? '-' }}</td><td class="px-4 py-3 text-right"><form method="POST" action="{{ route('tu.mapel-siswa.destroy', $a) }}" class="inline">@csrf @method('DELETE')<button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg"><x-heroicon-o-trash class="w-4 h-4" /></button></form></td></tr>@empty<tr><td colspan="2" class="px-4 py-12 text-center text-gray-400">Pilih mapel-kelas terlebih dahulu.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection