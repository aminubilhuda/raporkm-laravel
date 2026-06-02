@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-users class="w-7 h-7" />
            Anggota Kelas
        </h1>
        <p class="mt-1 text-sm text-gray-500">Daftar siswa per kelas.</p>
    </div>

    {{-- Pilih Kelas --}}
    @if($daftarKelas->isNotEmpty())
    <div class="flex flex-wrap items-center gap-2">
        <span class="text-sm font-bold text-gray-500 mr-1">Kelas:</span>
        @foreach($daftarKelas as $k)
            <a href="{{ route('guru.anggota-kelas.index', $k) }}"
                class="px-4 py-2 text-sm font-bold rounded-pill transition-all {{ $k->id === $kelas?->id ? 'bg-coral text-white shadow-coral-glow' : 'bg-white text-gray-600 border border-gray-200 hover:border-coral hover:text-coral' }}">
                {{ $k->nama_kelas }}
            </a>
        @endforeach
    </div>
    @endif

    {{-- Table Siswa --}}
    @if($kelas)
    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-surface-base flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-gray-700">{{ $kelas->nama_kelas }}</h3>
                <p class="text-xs text-gray-400">{{ $kelas->tingkat?->nama ?? '-' }} · {{ $kelas->kompetensiKeahlian?->nama_kompetensi ?? '-' }}</p>
            </div>
            <span class="text-sm font-bold text-gray-500">{{ $siswa->count() }} siswa</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden sm:table-cell">NISN</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden md:table-cell">NIS</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden lg:table-cell">JK</th>
                        <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($siswa as $index => $sk)
                    <tr class="hover:bg-coral/5 transition-colors">
                        <td class="px-4 py-3 text-gray-400 font-bold">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-coral/10 text-coral flex items-center justify-center flex-shrink-0">
                                    <x-heroicon-o-user class="w-4 h-4" />
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $sk->siswa?->nama_siswa ?? '-' }}</p>
                                    <p class="text-xs text-gray-400 sm:hidden">{{ $sk->siswa?->nisn ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $sk->siswa?->nisn ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $sk->siswa?->nis ?? '-' }}</td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <span class="px-2 py-0.5 text-xs font-bold rounded-pill {{ $sk->siswa?->kelamin == 1 ? 'bg-sky/10 text-sky' : 'bg-coral/10 text-coral' }}">
                                {{ $sk->siswa?->kelamin == 1 ? 'L' : 'P' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('tu.kesiswaan.show', $sk->siswa_id) }}" class="p-1.5 text-sky hover:bg-sky/5 rounded-lg transition-colors" title="Detail">
                                <x-heroicon-o-eye class="w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">Belum ada siswa di kelas ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
        <x-heroicon-o-users class="w-16 h-16 text-gray-300 mb-4" />
        <h2 class="text-xl font-extrabold text-gray-400 mb-2">Tidak Ada Kelas</h2>
        <p class="text-gray-400">Anda belum terdaftar sebagai wali atau pengajar kelas manapun.</p>
    </div>
    @endif
</div>
@endsection
