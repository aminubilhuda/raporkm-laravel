@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-arrows-up-down class="w-7 h-7" />
            Tingkat Kelas
        </h1>
        <p class="mt-1 text-sm text-gray-500">Kelola tingkat kelas (X, XI, XII) dan fase Kurikulum Merdeka.</p>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tambah Tingkat</h2>
        <form method="POST" action="{{ route('tu.tingkat.store') }}" class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">
            @csrf
            <div>
                <x-input-label for="nama" value="Nama" />
                <x-text-input id="nama" name="nama" :value="old('nama')" placeholder="X" class="block w-full mt-1" required />
            </div>
            <div>
                <x-input-label for="angka" value="Angka" />
                <x-text-input id="angka" name="angka" type="number" :value="old('angka')" placeholder="10" class="block w-full mt-1" required />
            </div>
            <div>
                <x-input-label for="fase" value="Fase KM" />
                <x-text-input id="fase" name="fase" :value="old('fase')" placeholder="E" class="block w-full mt-1" required />
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
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Angka</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Fase</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Urutan</th>
                    <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tingkat as $t)
                <tr class="hover:bg-teal-bg/30">
                    <td class="px-4 py-3 font-bold text-gray-800">{{ $t->nama }}</td>
                    <td class="px-4 py-3 text-teal-primary font-bold">{{ $t->angka }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs font-bold rounded-pill bg-gold/10 text-gold-dark">{{ $t->fase }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $t->urutan }}</td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="document.getElementById('edit-{{ $t->id }}').classList.toggle('hidden')"
                            class="p-1.5 text-sky hover:bg-sky/5 rounded-lg" title="Edit">
                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                        </button>
                        <form method="POST" action="{{ route('tu.tingkat.destroy', $t) }}" class="inline" onsubmit="return confirm('Hapus tingkat ini?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg" title="Hapus">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </form>
                    </td>
                </tr>
                <tr id="edit-{{ $t->id }}" class="hidden bg-surface-base/50">
                    <td colspan="5" class="px-4 py-3">
                        <form method="POST" action="{{ route('tu.tingkat.update', $t) }}" class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                            @csrf @method('PUT')
                            <div>
                                <x-input-label for="en_{{ $t->id }}" value="Nama" />
                                <x-text-input id="en_{{ $t->id }}" name="nama" :value="$t->nama" class="block w-full mt-1" required />
                            </div>
                            <div>
                                <x-input-label for="ea_{{ $t->id }}" value="Angka" />
                                <x-text-input id="ea_{{ $t->id }}" name="angka" type="number" :value="$t->angka" class="block w-full mt-1" required />
                            </div>
                            <div>
                                <x-input-label for="ef_{{ $t->id }}" value="Fase" />
                                <x-text-input id="ef_{{ $t->id }}" name="fase" :value="$t->fase" class="block w-full mt-1" required />
                            </div>
                            <div>
                                <x-input-label for="eu_{{ $t->id }}" value="Urutan" />
                                <x-text-input id="eu_{{ $t->id }}" name="urutan" type="number" :value="$t->urutan" class="block w-full mt-1" required />
                            </div>
                            <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">Simpan</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">Belum ada data tingkat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection