@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-squares-plus class="w-7 h-7" />
            Mapel Kelas
        </h1>
        <p class="mt-1 text-sm text-gray-500">Assign mata pelajaran ke kelas beserta guru pengajar.</p>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <div>
                <x-input-label for="kelas_id" value="Pilih Kelas" />
                <select id="kelas_id" name="kelas_id" onchange="this.form.submit()" class="mt-1 block w-full border-teal-primary/20 rounded-card">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelass as $k)<option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>@endforeach
                </select>
            </div>
        </form>

        @if($kelasId)
        <form method="POST" action="{{ route('tu.mapel-kelas.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end border-t border-gray-200 pt-4">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
            <div>
                <x-input-label for="mapel_id" value="Mata Pelajaran" />
                <select id="mapel_id" name="mapel_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                    <option value="">-- Pilih --</option>
                    @foreach($mapels as $m)<option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>@endforeach
                </select>
            </div>
            <div>
                <x-input-label for="user_id" value="Guru Pengajar" />
                <select id="user_id" name="user_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                    <option value="">-- Pilih --</option>
                    @foreach($gurus as $g)<option value="{{ $g->id }}">{{ $g->nama }}</option>@endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2 md:col-span-2">
                <x-heroicon-o-plus-circle class="w-5 h-5" /> Assign Mapel
            </button>
        </form>
        @endif
    </div>

    @if($kelasId)
    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-surface-base text-left">
                <tr>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Mapel</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden md:table-cell">Kode</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Guru</th>
                    <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($assignments as $a)
                <tr class="hover:bg-teal-bg/30">
                    <td class="px-4 py-3 font-bold text-gray-800">{{ $a->mapel->nama_mapel ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $a->mapel->kode ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $a->user->nama ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('tu.mapel-kelas.destroy', $a) }}" class="inline" onsubmit="return confirm('Hapus assign ini?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg"><x-heroicon-o-trash class="w-4 h-4" /></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada mapel di-assign.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection