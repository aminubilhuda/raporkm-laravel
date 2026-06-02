@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-user-group class="w-7 h-7" />
            Anggota Kelas
        </h1>
        <p class="mt-1 text-sm text-gray-500">Kelola siswa dalam setiap rombongan belajar.</p>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <div>
                <x-input-label for="kelas_id" value="Pilih Kelas" />
                <select id="kelas_id" name="kelas_id" onchange="this.form.submit()" class="mt-1 block w-full border-teal-primary/20 rounded-card">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelass as $k)
                        <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }} ({{ $k->tingkat->nama ?? '' }} {{ $k->kompetensiKeahlian->singkatan ?? '' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($kelasId && $anggota->count())
        <form method="POST" action="{{ route('tu.anggota-kelas.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end border-t border-gray-200 pt-4">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
            <div class="md:col-span-2">
                <x-input-label for="siswa_id" value="Tambah Siswa ke Kelas" />
                <select id="siswa_id" name="siswa_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                    <option value="">-- Cari Siswa --</option>
                    @foreach(\App\Models\Siswa::where('aktif',1)->orderBy('nama_siswa')->get() as $s)
                        @unless($anggota->pluck('siswa_id')->contains($s->id))
                            <option value="{{ $s->id }}">{{ $s->nama_siswa }} ({{ $s->nisn }})</option>
                        @endunless
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">
                <x-heroicon-o-plus-circle class="w-5 h-5" /> Tambahkan
            </button>
        </form>
        @endif
    </div>

    @if($kelasId)
    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-surface-base text-left">
                <tr>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">NISN</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden sm:table-cell">Status</th>
                    <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($anggota as $i => $a)
                <tr class="hover:bg-teal-bg/30">
                    <td class="px-4 py-3 text-gray-400">{{ $i+1 }}</td>
                    <td class="px-4 py-3 font-bold text-gray-800">{{ $a->siswa->nama_siswa ?? '-' }}</td>
                    <td class="px-4 py-3 text-teal-primary font-bold">{{ $a->siswa->nisn ?? '-' }}</td>
                    <td class="px-4 py-3 hidden sm:table-cell"><span class="text-xs font-bold text-success">{{ $a->status }}</span></td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('tu.anggota-kelas.destroy', $a) }}" class="inline" onsubmit="return confirm('Keluarkan dari kelas?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg"><x-heroicon-o-x-circle class="w-4 h-4" /></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">Belum ada siswa di kelas ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection