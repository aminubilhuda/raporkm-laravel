@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-book-open class="w-7 h-7" />
            Mata Pelajaran
        </h1>
        <p class="mt-1 text-sm text-gray-500">Kelola data mata pelajaran.</p>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tambah Mapel</h2>
        <form method="POST" action="{{ route('tu.mapel.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            @csrf
            <div>
                <x-input-label for="kelompok_mapel_id" value="Kelompok" />
                <select id="kelompok_mapel_id" name="kelompok_mapel_id" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20" required>
                    <option value="">-- Pilih --</option>
                    @foreach($kelompok as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="kode" value="Kode" />
                <x-text-input id="kode" name="kode" :value="old('kode')" placeholder="MTK" class="block w-full mt-1" />
            </div>
            <div>
                <x-input-label for="nama_mapel" value="Nama Mapel" />
                <x-text-input id="nama_mapel" name="nama_mapel" :value="old('nama_mapel')" placeholder="Matematika" class="block w-full mt-1" required />
            </div>
            <div>
                <x-input-label for="kkm" value="KKM" />
                <x-text-input id="kkm" name="kkm" type="number" :value="old('kkm', 75)" class="block w-full mt-1" required />
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
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Kode</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama Mapel</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden sm:table-cell">Kelompok</th>
                    <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">KKM</th>
                    <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($mapel as $m)
                <tr class="hover:bg-teal-bg/30">
                    <td class="px-4 py-3"><span class="px-2 py-0.5 text-xs font-bold rounded-pill bg-teal-primary/10 text-teal-primary">{{ $m->kode ?? '-' }}</span></td>
                    <td class="px-4 py-3 font-bold text-gray-800">{{ $m->nama_mapel }}</td>
                    <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $m->kelompokMapel->nama ?? '-' }}</td>
                    <td class="px-4 py-3 text-teal-primary font-bold">{{ $m->kkm }}</td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="document.getElementById('edit-{{ $m->id }}').classList.toggle('hidden')" class="p-1.5 text-sky hover:bg-sky/5 rounded-lg"><x-heroicon-o-pencil-square class="w-4 h-4" /></button>
                        <form method="POST" action="{{ route('tu.mapel.destroy', $m) }}" class="inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg"><x-heroicon-o-trash class="w-4 h-4" /></button>
                        </form>
                    </td>
                </tr>
                <tr id="edit-{{ $m->id }}" class="hidden bg-surface-base/50">
                    <td colspan="5" class="px-4 py-3">
                        <form method="POST" action="{{ route('tu.mapel.update', $m) }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                            @csrf @method('PUT')
                            <div>
                                <select name="kelompok_mapel_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                                    @foreach($kelompok as $k)
                                        <option value="{{ $k->id }}" {{ $m->kelompok_mapel_id == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div><x-text-input name="kode" :value="$m->kode" class="block w-full mt-1" /></div>
                            <div><x-text-input name="nama_mapel" :value="$m->nama_mapel" class="block w-full mt-1" required /></div>
                            <div><x-text-input name="kkm" type="number" :value="$m->kkm" class="block w-full mt-1" required /></div>
                            <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">Simpan</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">Belum ada data mapel.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $mapel->links() }}</div>
    </div>
</div>
@endsection