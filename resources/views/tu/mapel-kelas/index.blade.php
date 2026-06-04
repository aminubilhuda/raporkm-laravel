@extends('layouts.tu')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
                <x-heroicon-o-squares-plus class="w-7 h-7" /> Mapel Kelas
            </h1>
            <p class="mt-1 text-sm text-gray-500">Atur mata pelajaran, guru pengajar, KKM, dan urutan per kelas.</p>
        </div>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 items-end">
            <div>
                <x-input-label for="kelas_id" value="Pilih Kelas" />
                <select id="kelas_id" name="kelas_id" onchange="this.form.submit()" class="mt-1 block w-full border-teal-primary/20 rounded-card">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelass as $k)
                        <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if($kelasId)
    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tambah Mapel ke Kelas</h2>
        <form method="POST" action="{{ route('tu.mapel-kelas.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
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
                <select id="user_id" name="user_id" class="mt-1 block w-full border-teal-primary/20 rounded-card">
                    <option value="">-- Pilih --</option>
                    @foreach($gurus as $g)<option value="{{ $g->id }}">{{ $g->nama }}</option>@endforeach
                </select>
            </div>
            <div>
                <x-input-label for="kkm" value="KKM" />
                <x-text-input id="kkm" name="kkm" type="number" value="75" min="0" max="100" class="block w-full mt-1" />
            </div>
            <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2 md:col-span-2">
                <x-heroicon-o-plus-circle class="w-5 h-5" /> Tambah Mapel
            </button>
        </form>
    </div>

    @if($assignments->isNotEmpty())
    <div class="bg-white rounded-card shadow-card overflow-hidden border-l-[6px] border-l-teal-primary">
        <form method="POST" action="{{ route('tu.mapel-kelas.batch-update') }}">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelasId }}">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-base text-left">
                        <tr>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-10">No</th>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Mapel</th>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Guru Pengajar</th>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">KKM</th>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Urutan</th>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($assignments as $i => $a)
                        <tr class="hover:bg-teal-bg/30">
                            <td class="px-3 py-2 text-gray-500 text-center">{{ $loop->iteration }}</td>
                            <td class="px-3 py-2 font-bold text-gray-800">{{ $a->mapel->nama_mapel ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <input type="hidden" name="assignments[{{ $i }}][id]" value="{{ $a->id }}">
                                <select name="assignments[{{ $i }}][user_id]" class="block w-full border-teal-primary/20 bg-white rounded-card shadow-sm focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 transition-colors text-sm py-1.5">
                                    <option value="">-- Pilih --</option>
                                    @foreach($gurus as $g)
                                        <option value="{{ $g->id }}" {{ $a->user_id == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" name="assignments[{{ $i }}][kkm]" value="{{ $a->kkm ?: 75 }}" min="0" max="100" class="block w-20 border-teal-primary/20 bg-white rounded-card shadow-sm focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 transition-colors text-sm py-1.5 text-center">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" name="assignments[{{ $i }}][urutan]" value="{{ $a->mapel->urutan ?? '' }}" min="0" class="block w-20 border-teal-primary/20 bg-white rounded-card shadow-sm focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 transition-colors text-sm py-1.5 text-center" readonly>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button type="button" onclick="deleteMapelKelas({{ $a->id }})" class="p-1.5 text-coral hover:bg-coral/5 rounded-lg"><x-heroicon-o-trash class="w-4 h-4" /></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-end gap-3">
                <span class="text-sm text-gray-500">{{ $assignments->count() }} mapel</span>
                <button type="submit" class="btn-primary inline-flex items-center gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5" /> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
    @endif

    @if($assignments->isEmpty())
    <div class="bg-white rounded-card shadow-card p-12 text-center border-l-[6px] border-l-teal-primary">
        <x-heroicon-o-squares-plus class="w-16 h-16 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-400">Belum ada mapel di-assign ke kelas ini. Tambah mapel menggunakan form di atas.</p>
    </div>
    @endif
    @endif
</div>

<script>
    async function deleteMapelKelas(id) {
        if (!confirm('Hapus mapel ini dari kelas?')) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("tu/mapel-kelas") }}/' + id;

        const token = document.querySelector('meta[name="csrf-token"]').content;
        form.innerHTML = '<input type="hidden" name="_token" value="' + token + '"><input type="hidden" name="_method" value="DELETE">';

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection
