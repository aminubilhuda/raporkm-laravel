@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-building-office-2 class="w-7 h-7" />
            Kelas / Rombel
        </h1>
        <p class="mt-1 text-sm text-gray-500">Kelola rombongan belajar (kelas) per tingkat dan jurusan.</p>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tambah Rombel</h2>
        <form method="POST" action="{{ route('tu.rombel.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            @csrf
            <div>
                <x-input-label for="tingkat_id" value="Tingkat" />
                <select id="tingkat_id" name="tingkat_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                    <option value="">-- Pilih --</option>
                    @foreach($tingkats as $t)<option value="{{ $t->id }}">{{ $t->nama }} (Fase {{ $t->fase }})</option>@endforeach
                </select>
            </div>
            <div>
                <x-input-label for="kompetensi_keahlian_id" value="Jurusan" />
                <select id="kompetensi_keahlian_id" name="kompetensi_keahlian_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                    <option value="">-- Pilih --</option>
                    @foreach($kompetensis as $k)<option value="{{ $k->id }}">{{ $k->singkatan ?? $k->nama }}</option>@endforeach
                </select>
            </div>
            <div>
                <x-input-label for="nama_kelas" value="Nama Kelas" />
                <x-text-input id="nama_kelas" name="nama_kelas" :value="old('nama_kelas')" placeholder="X TKJ A" class="block w-full mt-1" required />
            </div>
            <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">
                <x-heroicon-o-plus-circle class="w-5 h-5" /> Tambah
            </button>
        </form>
    </div>

    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama Kelas</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Tingkat</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden md:table-cell">Jurusan</th>
                        <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rombels as $r)
                    <tr class="hover:bg-teal-bg/30">
                        <td class="px-4 py-3 font-bold text-gray-800">{{ $r->nama_kelas }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 text-xs font-bold rounded-pill bg-teal-primary/10 text-teal-primary">{{ $r->tingkat->nama ?? '-' }}</span></td>
                        <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $r->kompetensiKeahlian->singkatan ?? $r->kompetensiKeahlian->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="document.getElementById('edit-{{ $r->id }}').classList.toggle('hidden')" class="p-1.5 text-sky hover:bg-sky/5 rounded-lg"><x-heroicon-o-pencil-square class="w-4 h-4" /></button>
                            <form method="POST" action="{{ route('tu.rombel.destroy', $r) }}" class="inline" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg"><x-heroicon-o-trash class="w-4 h-4" /></button>
                            </form>
                        </td>
                    </tr>
                    <tr id="edit-{{ $r->id }}" class="hidden bg-surface-base/50">
                        <td colspan="4" class="px-4 py-3">
                            <form method="POST" action="{{ route('tu.rombel.update', $r) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                @csrf @method('PUT')
                                <div>
                                    <select name="tingkat_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                                        @foreach($tingkats as $t)<option value="{{ $t->id }}" {{ $r->tingkat_id == $t->id ? 'selected' : '' }}>{{ $t->nama }}</option>@endforeach
                                    </select>
                                </div>
                                <div>
                                    <select name="kompetensi_keahlian_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                                        @foreach($kompetensis as $k)<option value="{{ $k->id }}" {{ $r->kompetensi_keahlian_id == $k->id ? 'selected' : '' }}>{{ $k->singkatan ?? $k->nama }}</option>@endforeach
                                    </select>
                                </div>
                                <div><x-text-input name="nama_kelas" :value="$r->nama_kelas" class="block w-full mt-1" required /></div>
                                <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">Simpan</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada data rombel.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $rombels->links() }}</div>
    </div>
</div>
@endsection