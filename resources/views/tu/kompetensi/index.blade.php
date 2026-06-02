@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-building-office class="w-7 h-7" />
            Kompetensi Keahlian
        </h1>
        <p class="mt-1 text-sm text-gray-500">Kelola program keahlian / jurusan.</p>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tambah Kompetensi</h2>
        <form method="POST" action="{{ route('tu.kompetensi.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            @csrf
            <div>
                <x-input-label for="nama" value="Nama" />
                <x-text-input id="nama" name="nama" :value="old('nama')" placeholder="Teknik Komputer dan Jaringan" class="block w-full mt-1" required />
            </div>
            <div>
                <x-input-label for="singkatan" value="Singkatan" />
                <x-text-input id="singkatan" name="singkatan" :value="old('singkatan')" placeholder="TKJ" class="block w-full mt-1" />
            </div>
            <div>
                <x-input-label for="keterangan" value="Keterangan" />
                <x-text-input id="keterangan" name="keterangan" :value="old('keterangan')" class="block w-full mt-1" />
            </div>
            <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">
                <x-heroicon-o-plus-circle class="w-5 h-5" /> Tambah
            </button>
        </form>
    </div>

    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-surface-base text-left">
                <tr>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Singkatan</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden md:table-cell">Keterangan</th>
                    <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($kompetensi as $k)
                <tr class="hover:bg-teal-bg/30">
                    <td class="px-4 py-3 font-bold text-gray-800">{{ $k->nama }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs font-bold rounded-pill bg-teal-primary/10 text-teal-primary">{{ $k->singkatan ?? '-' }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $k->keterangan ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="document.getElementById('edit-{{ $k->id }}').classList.toggle('hidden')" class="p-1.5 text-sky hover:bg-sky/5 rounded-lg"><x-heroicon-o-pencil-square class="w-4 h-4" /></button>
                        <form method="POST" action="{{ route('tu.kompetensi.destroy', $k) }}" class="inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg"><x-heroicon-o-trash class="w-4 h-4" /></button>
                        </form>
                    </td>
                </tr>
                <tr id="edit-{{ $k->id }}" class="hidden bg-surface-base/50">
                    <td colspan="4" class="px-4 py-3">
                        <form method="POST" action="{{ route('tu.kompetensi.update', $k) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                            @csrf @method('PUT')
                            <div><x-text-input name="nama" :value="$k->nama" class="block w-full mt-1" required /></div>
                            <div><x-text-input name="singkatan" :value="$k->singkatan" class="block w-full mt-1" /></div>
                            <div class="hidden md:block"><x-text-input name="keterangan" :value="$k->keterangan" class="block w-full mt-1" /></div>
                            <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">Simpan</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection