@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-academic-cap class="w-7 h-7" />
            Nilai Prakerin
        </h1>
        <p class="mt-1 text-sm text-gray-500">Input nilai siswa yang Anda bimbing dalam program Prakerin.</p>
    </div>

    @if($daftarSiswa->isEmpty())
    <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
        <x-heroicon-o-building-office class="w-16 h-16 text-gray-300 mb-4" />
        <h2 class="text-xl font-extrabold text-gray-400 mb-2">Belum Ada Siswa Prakerin</h2>
        <p class="text-gray-400 max-w-md">Anda belum ditugasi sebagai pembimbing siswa prakerin. Hubungi TU untuk menugaskan siswa ke Anda.</p>
    </div>
    @else
    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
            <h3 class="font-extrabold text-gray-700">Daftar Siswa Bimbingan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Siswa</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Kelas</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Perusahaan</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Status</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($daftarSiswa as $sp)
                    <tr class="hover:bg-coral/5 transition-colors">
                        <td class="px-4 py-3 font-bold text-gray-700">{{ $sp->siswa->nama_siswa ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $sp->kelas->nama_kelas ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $sp->prakerin->nama_perusahaan ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold {{ $sp->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($sp->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($sp->status === 'aktif')
                            <a href="{{ route('guru.nilai-prakerin.edit', $sp) }}" class="text-coral hover:text-coral-dark font-bold text-xs inline-flex items-center gap-1">
                                <x-heroicon-o-pencil class="w-4 h-4" /> Input Nilai
                            </a>
                            @else
                            <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection