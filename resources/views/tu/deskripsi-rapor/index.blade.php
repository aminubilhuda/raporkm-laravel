@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-pencil-square class="w-7 h-7" />
            Deskripsi Rapor
        </h1>
        <p class="mt-1 text-sm text-gray-500">Kelola template deskripsi otomatis untuk setiap predikat nilai.</p>
    </div>

    {{-- Form tambah --}}
    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tambah Template</h2>
        <form method="POST" action="{{ route('tu.deskripsi-rapor.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            @csrf
            <div>
                <x-input-label for="nama" value="Nama Template" />
                <x-text-input id="nama" name="nama" :value="old('nama')" class="block w-full mt-1" required />
            </div>
            <div>
                <x-input-label for="kktp" value="KKTP" />
                <x-text-input id="kktp" name="kktp" type="number" :value="old('kktp', 75)" class="block w-full mt-1" required />
            </div>
            <div>
                <x-input-label for="predikat" value="Predikat" />
                <select id="predikat" name="predikat" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20" required>
                    <option value="Sangat Baik" {{ old('predikat') == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                    <option value="Baik" {{ old('predikat') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Cukup" {{ old('predikat') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                    <option value="Perlu Bimbingan" {{ old('predikat') == 'Perlu Bimbingan' ? 'selected' : '' }}>Perlu Bimbingan</option>
                </select>
            </div>
            <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">
                <x-heroicon-o-plus-circle class="w-5 h-5" />
                Tambah
            </button>
            <div class="md:col-span-4">
                <x-input-label for="deskripsi" value="Teks Deskripsi" />
                <textarea id="deskripsi" name="deskripsi" rows="2" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20" required>{{ old('deskripsi') }}</textarea>
                <x-input-error :messages="$errors->get('deskripsi')" class="mt-1" />
            </div>
        </form>
    </div>

    {{-- Daftar --}}
    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">KKTP</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Predikat</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden md:table-cell">Deskripsi</th>
                        <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($deskripsi as $d)
                    <tr class="hover:bg-teal-bg/30 transition-colors">
                        <td class="px-4 py-3 font-bold text-gray-800">{{ $d->nama }}</td>
                        <td class="px-4 py-3 text-teal-primary font-bold">{{ $d->kktp }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs font-bold rounded-pill
                                {{ $d->predikat == 'Sangat Baik' ? 'bg-teal-primary/10 text-teal-primary' : '' }}
                                {{ $d->predikat == 'Baik' ? 'bg-gold/10 text-gold-dark' : '' }}
                                {{ $d->predikat == 'Cukup' ? 'bg-sky/10 text-sky' : '' }}
                                {{ $d->predikat == 'Perlu Bimbingan' ? 'bg-coral/10 text-coral' : '' }}">
                                {{ $d->predikat }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 hidden md:table-cell max-w-xs truncate">{{ $d->deskripsi }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button onclick="document.getElementById('edit-form-{{ $d->id }}').classList.toggle('hidden')"
                                    class="p-1.5 text-sky hover:bg-sky/5 rounded-lg transition-colors" title="Edit">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>
                                <form method="POST" action="{{ route('tu.deskripsi-rapor.destroy', $d) }}" class="inline" onsubmit="return confirm('Hapus template ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg transition-colors" title="Hapus">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr id="edit-form-{{ $d->id }}" class="hidden bg-surface-base/50">
                        <td colspan="5" class="px-4 py-3">
                            <form method="POST" action="{{ route('tu.deskripsi-rapor.update', $d) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                @csrf @method('PUT')
                                <div>
                                    <x-input-label for="edit_nama_{{ $d->id }}" value="Nama" />
                                    <x-text-input id="edit_nama_{{ $d->id }}" name="nama" :value="$d->nama" class="block w-full mt-1" required />
                                </div>
                                <div>
                                    <x-input-label for="edit_kktp_{{ $d->id }}" value="KKTP" />
                                    <x-text-input id="edit_kktp_{{ $d->id }}" name="kktp" type="number" :value="$d->kktp" class="block w-full mt-1" required />
                                </div>
                                <div>
                                    <x-input-label for="edit_predikat_{{ $d->id }}" value="Predikat" />
                                    <select id="edit_predikat_{{ $d->id }}" name="predikat" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20" required>
                                        @foreach(['Sangat Baik','Baik','Cukup','Perlu Bimbingan'] as $p)
                                            <option value="{{ $p }}" {{ $d->predikat == $p ? 'selected' : '' }}>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">
                                    <x-heroicon-o-check-circle class="w-5 h-5" />
                                    Simpan
                                </button>
                                <div class="md:col-span-4">
                                    <x-input-label for="edit_deskripsi_{{ $d->id }}" value="Deskripsi" />
                                    <textarea id="edit_deskripsi_{{ $d->id }}" name="deskripsi" rows="2" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20" required>{{ $d->deskripsi }}</textarea>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400">Belum ada template deskripsi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection