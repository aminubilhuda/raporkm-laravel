@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3 border-b-2 border-solid border-teal-primary/25 pb-4">
        <span class="icon-circle icon-circle-teal w-9 h-9 rounded-field"><x-heroicon-o-user-group class="w-5 h-5" /></span>
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark">Anggota Kelas</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola siswa dalam setiap rombongan belajar.</p>
        </div>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <form method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <x-input-label for="search" value="Cari Siswa" />
                    <div class="flex gap-2 mt-1">
                        <input type="text" id="search" name="search" value="{{ $search ?? '' }}"
                               placeholder="Nama atau NISN..."
                               class="block w-full border-teal-primary/20 rounded-field bg-cream focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 px-3 py-2 text-sm">
                        <button type="submit" class="btn-primary inline-flex items-center justify-center gap-1.5 px-4 whitespace-nowrap rounded-pill text-sm">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" /> Cari
                        </button>
                    </div>
                </div>
                <div>
                    <x-input-label for="tingkat_id" value="Tingkat" />
                    <select id="tingkat_id" name="tingkat_id" onchange="this.form.submit()"
                            class="mt-1 block w-full border-teal-primary/20 rounded-field bg-cream focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                        <option value="">Semua Tingkat</option>
                        @foreach($tingkats as $t)
                            <option value="{{ $t->id }}" {{ $tingkatId == $t->id ? 'selected' : '' }}>{{ $t->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="jurusan_id" value="Program Keahlian" />
                    <select id="jurusan_id" name="jurusan_id" onchange="this.form.submit()"
                            class="mt-1 block w-full border-teal-primary/20 rounded-field bg-cream focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" {{ $jurusanId == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <x-input-label for="kelas_id" value="Pilih Kelas" />
                <div class="flex gap-2 mt-1">
                    <select id="kelas_id" name="kelas_id" onchange="this.form.submit()"
                            class="block w-full border-teal-primary/20 rounded-field bg-cream focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                        <option value="">-- Pilih Kelas --</option>
                        @forelse($kelass as $k)
                            <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }} ({{ $k->tingkat->nama ?? '' }} {{ $k->kompetensiKeahlian->singkatan ?? '' }})
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada kelas sesuai filter</option>
                        @endforelse
                    </select>
                    @if($tingkatId || $jurusanId || $search)
                    <a href="{{ route('tu.anggota-kelas.index') }}" class="btn-coral inline-flex items-center justify-center gap-1.5 px-4 whitespace-nowrap rounded-pill text-sm" title="Reset filter">
                        <x-heroicon-o-x-mark class="w-4 h-4" /> Reset
                    </a>
                    @endif
                </div>
            </div>
        </form>

        @if($kelasId)
        <form method="POST" action="{{ route('tu.anggota-kelas.store') }}" class="border-t border-solid border-teal-primary/20 pt-4">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <x-input-label for="siswa_id" value="Tambah Siswa ke Kelas" />
                    <select id="siswa_id" name="siswa_id" class="select2-anggota mt-1 block w-full border-teal-primary/20 rounded-field" data-placeholder="Cari Siswa..." required>
                        <option value=""></option>
                        @forelse($availableSiswa as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_siswa }} ({{ $s->nisn }})</option>
                        @empty
                            <option value="" disabled>Semua siswa sudah terdaftar di kelas</option>
                        @endforelse
                    </select>
                </div>
                <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2 whitespace-nowrap">
                    <x-heroicon-o-plus-circle class="w-5 h-5" /> Tambahkan
                </button>
            </div>
        </form>
        @endif
    </div>

    @if($kelasId)
    <div class="bg-white rounded-card shadow-card overflow-hidden border-l-[6px] border-l-teal-primary">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left border-b-2 border-solid border-teal-primary/20">
                    <tr>
                        <th class="px-4 py-3.5 font-extrabold text-gray-500 text-xs uppercase tracking-wider">#</th>
                        <th class="px-4 py-3.5 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3.5 font-extrabold text-gray-500 text-xs uppercase tracking-wider">NISN</th>
                        <th class="px-4 py-3.5 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden sm:table-cell">Status</th>
                        <th class="px-4 py-3.5 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($anggota as $i => $a)
                    <tr class="hover:bg-teal-bg/30 transition-colors">
                        <td class="px-4 py-3.5 text-gray-400">{{ $i+1 }}</td>
                        <td class="px-4 py-3.5 font-bold text-gray-800">{{ $a->siswa->nama_siswa ?? '-' }}</td>
                        <td class="px-4 py-3.5 text-teal-primary font-bold">{{ $a->siswa->nisn ?? '-' }}</td>
                        <td class="px-4 py-3.5 hidden sm:table-cell">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-pill bg-success/10 text-success shadow-sm">{{ $a->status }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            <a href="{{ route('tu.rapor.identitas', $a->siswa_id) }}" target="_blank"
                               class="p-2 text-teal-primary hover:bg-teal-primary/10 rounded-pill transition-colors inline-block"
                               title="Cetak Identitas Siswa">
                                <x-heroicon-o-printer class="w-4 h-4" />
                            </a>
                            <form method="POST" action="{{ route('tu.anggota-kelas.destroy', $a) }}" class="inline" onsubmit="return confirm('Keluarkan {{ $a->siswa->nama_siswa ?? 'siswa ini' }} dari kelas?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-coral hover:bg-coral/10 rounded-pill transition-colors" title="Keluarkan">
                                    <x-heroicon-o-x-circle class="w-4 h-4" />
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center">
                            <x-heroicon-o-academic-cap class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                            @if($search)
                                <p class="text-sm font-semibold text-gray-400">Tidak ada siswa yang cocok dengan <span class="font-bold text-teal-primary">"{{ $search }}"</span></p>
                                <p class="text-xs text-gray-300 mt-1">Coba gunakan kata kunci lain.</p>
                            @else
                                <p class="text-sm font-semibold text-gray-400">Belum ada siswa di kelas ini.</p>
                                <p class="text-xs text-gray-300 mt-1">Gunakan form di atas untuk menambahkan siswa.</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
<script>
document.addEventListener('jquery-ready', function() {
    $('.select2-anggota').each(function() {
        var $el = $(this);
        $el.select2({
            placeholder: $el.data('placeholder') || 'Cari...',
            allowClear: true,
            width: '100%',
        });
    });
});
</script>
@endsection