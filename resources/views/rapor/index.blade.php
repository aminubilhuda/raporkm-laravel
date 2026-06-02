@extends(auth()->user()->jabatan == 2 ? 'layouts.tu' : 'layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold {{ auth()->user()->jabatan == 2 ? 'text-teal-primary-dark' : 'text-coral-dark' }} flex items-center gap-2">
            <x-heroicon-o-printer class="w-7 h-7" />
            Cetak Rapor
        </h1>
        <p class="mt-1 text-sm text-gray-500">Pilih siswa untuk mencetak rapor.</p>
    </div>

    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
            <h3 class="font-extrabold text-gray-700">{{ $kelas->nama_kelas }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden sm:table-cell">NISN</th>
                        <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse(\App\Models\SiswaKelas::where('kelas_id', $kelas->id)
                        ->where('tahun_pelajaran_id', $taId)
                        ->where('semester_id', $semesterId)
                        ->with('siswa')->get() as $i => $sk)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-400 font-bold">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-bold text-gray-800">{{ $sk->siswa->nama_siswa ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $sk->siswa->nisn ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('rapor.pdf', ['kelas' => $kelas, 'siswa' => $sk->siswa_id]) }}"
                                    class="px-3 py-1.5 text-xs font-bold rounded-pill bg-teal-primary text-white hover:bg-teal-dark transition-colors inline-flex items-center gap-1">
                                    <x-heroicon-o-document-text class="w-3.5 h-3.5" />
                                    Semester
                                </a>
                                <a href="{{ route('rapor.pdf-mid', ['kelas' => $kelas, 'siswa' => $sk->siswa_id]) }}"
                                    class="px-3 py-1.5 text-xs font-bold rounded-pill bg-gold text-white hover:bg-gold-dark transition-colors inline-flex items-center gap-1">
                                    <x-heroicon-o-document-arrow-up class="w-3.5 h-3.5" />
                                    Mid
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada siswa di kelas ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
