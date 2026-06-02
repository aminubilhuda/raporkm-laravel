@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-arrow-up-circle class="w-7 h-7" />
            Naik Kelas
        </h1>
        <p class="mt-1 text-sm text-gray-500">Proses kenaikan kelas siswa ke tingkat berikutnya.</p>
    </div>

    <form method="GET" id="naikForm">
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Pilih Kelas Asal</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="dari_kelas" value="Dari Kelas" />
                    <select id="dari_kelas" name="dari_kelas" onchange="document.getElementById('naikForm').submit()" class="mt-1 block w-full border-teal-primary/20 rounded-card">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelass as $k)<option value="{{ $k->id }}" {{ request('dari_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>@endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>

    @php $dariKelas = request('dari_kelas'); @endphp
    @if($dariKelas)
        @php
            $siswaList = \App\Models\SiswaKelas::with('siswa')
                ->where('kelas_id', $dariKelas)
                ->where('status', 'aktif')
                ->whereNull('deleted_at')
                ->get();
        @endphp

        <form method="POST" action="{{ route('tu.naik-kelas.process') }}">
            @csrf
            <input type="hidden" name="dari_kelas_id" value="{{ $dariKelas }}">

            <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold mt-4">
                <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Ke Kelas Tujuan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="ke_kelas_id" value="Naik ke Kelas" />
                        <select id="ke_kelas_id" name="ke_kelas_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                            <option value="">-- Pilih Kelas Tujuan --</option>
                            @foreach($kelass->where('id', '!=', (int)$dariKelas) as $k)<option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>@endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-card shadow-card overflow-hidden mt-4">
                <table class="w-full text-sm">
                    <thead class="bg-surface-base text-left">
                        <tr>
                            <th class="px-4 py-3"><input type="checkbox" onclick="document.querySelectorAll('.siswa-check').forEach(c=>c.checked=this.checked)" class="rounded"></th>
                            <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">NISN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($siswaList as $s)
                        <tr class="hover:bg-teal-bg/30">
                            <td class="px-4 py-3"><input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" class="siswa-check rounded"></td>
                            <td class="px-4 py-3 font-bold text-gray-800">{{ $s->siswa->nama_siswa ?? '-' }}</td>
                            <td class="px-4 py-3 text-teal-primary font-bold">{{ $s->siswa->nisn ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-4 py-12 text-center text-gray-400">Tidak ada siswa di kelas ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($siswaList->count())
            <div class="flex justify-end mt-4">
                <x-primary-button type="submit">Proses Kenaikan Kelas</x-primary-button>
            </div>
            @endif
        </form>
    @endif
</div>
@endsection