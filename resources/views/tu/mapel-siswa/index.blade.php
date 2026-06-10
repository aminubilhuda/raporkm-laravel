@extends('layouts.tu')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
                <x-heroicon-o-queue-list class="w-7 h-7" />
                Peta Mapel Siswa
            </h1>
            <p class="mt-1 text-sm text-gray-500">Petakan mata pelajaran untuk setiap siswa secara massal.</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" class="bg-white rounded-card shadow-card p-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1 font-extrabold uppercase">Tahun Pelajaran</label>
                <select name="tahun_pelajaran_id" onchange="this.form.submit()" class="w-full border-teal-primary/20 rounded-field text-sm py-2 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 bg-cream">
                    @foreach($tpList as $tp)
                        <option value="{{ $tp->id }}" {{ $selectedTp == $tp->id ? 'selected' : '' }}>{{ $tp->tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1 font-extrabold uppercase">Semester</label>
                <select name="semester_id" onchange="this.form.submit()" class="w-full border-teal-primary/20 rounded-field text-sm py-2 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 bg-cream">
                    @foreach($semesterList as $s)
                        <option value="{{ $s->id }}" {{ $selectedSemester == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1 font-extrabold uppercase">Kelas</label>
                <select name="kelas_id" onchange="this.form.submit()" class="w-full border-teal-primary/20 rounded-field text-sm py-2 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 bg-cream" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ $selectedKelas == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary flex-1 py-2 text-sm justify-center inline-flex items-center gap-2">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" /> Cari
                </button>
                <a href="{{ route('tu.mapel-siswa.index') }}" class="btn-secondary py-2 text-sm text-center">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Matrix Table --}}
    @if($selectedKelas && $siswaList->isNotEmpty() && $mapelList->isNotEmpty())
        <div class="bg-white rounded-card shadow-card overflow-hidden border-l-[6px] border-l-teal-primary">
            <form method="POST" action="{{ route('tu.mapel-siswa.batch-update') }}">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $selectedKelas }}">
                <input type="hidden" name="tahun_pelajaran_id" value="{{ $selectedTp }}">
                <input type="hidden" name="semester_id" value="{{ $selectedSemester }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-surface-base">
                            <tr>
                                <th class="px-4 py-3 font-extrabold text-xs uppercase text-gray-500 text-left sticky left-0 bg-surface-base z-10 min-w-[200px]">Nama Siswa</th>
                                @foreach($mapelList as $mk)
                                    <th class="px-3 py-3 font-extrabold text-xs uppercase text-gray-500 text-center whitespace-nowrap" title="{{ $mk->mapel->nama_mapel }}">
                                        <div class="flex flex-col items-center gap-1">
                                            <span>{{ $mk->short_name }}</span>
                                            <input type="checkbox" onclick="toggleColumn(this, 'col-{{ $mk->id }}')" class="rounded border-teal-primary/20 text-teal-primary focus:ring-teal-primary/30 w-3.5 h-3.5 cursor-pointer">
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($siswaList as $s)
                                <tr class="hover:bg-teal-bg/20">
                                    <td class="px-4 py-3 font-bold text-gray-800 sticky left-0 bg-white hover:bg-teal-bg/20 z-10 border-r border-gray-100">{{ $s->nama_siswa }}</td>
                                    @foreach($mapelList as $mk)
                                        @php
                                            $checked = isset($assignments[$s->id][$mk->id]);
                                        @endphp
                                        <td class="px-3 py-3 text-center">
                                            <input type="checkbox" name="mapel[{{ $s->id }}][{{ $mk->id }}]" value="1" {{ $checked ? 'checked' : '' }} class="col-{{ $mk->id }} rounded border-teal-primary/20 text-teal-primary focus:ring-teal-primary/30 w-4 h-4 cursor-pointer">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-surface-base/10">
                    <div class="text-xs text-gray-400">
                        * Centang checkbox pada kolom header untuk memilih semua siswa untuk mapel tersebut.
                    </div>
                    <button type="submit" class="btn-primary inline-flex items-center gap-2">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                        Simpan Penugasan Mapel
                    </button>
                </div>
            </form>
        </div>

        <script>
            function toggleColumn(source, colClass) {
                document.querySelectorAll('.' + colClass).forEach(cb => {
                    cb.checked = source.checked;
                });
            }
        </script>
    @elseif($selectedKelas)
        <div class="bg-white rounded-card shadow-card p-12 text-center">
            <x-heroicon-o-exclamation-circle class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <p class="text-gray-500">Tidak ada data siswa atau mata pelajaran yang aktif untuk kelas ini.</p>
            <p class="text-xs text-gray-400 mt-1">Pastikan kelas sudah memiliki anggota rombel and mata pelajaran terdaftar.</p>
        </div>
    @else
        <div class="bg-white rounded-card shadow-card p-12 text-center">
            <x-heroicon-o-queue-list class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <p class="text-gray-500">Silakan pilih Tahun Pelajaran, Semester, dan Kelas terlebih dahulu.</p>
        </div>
    @endif
</div>
@endsection
