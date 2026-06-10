@extends('layouts.tu')
@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-clipboard-document-list class="w-7 h-7" /> Rekap Absensi Guru & TU
        </h1>
        <p class="mt-1 text-sm text-gray-500">Rekapitulasi kehadiran guru dan tata usaha.</p>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-coral">
        <h2 class="font-extrabold text-coral-dark mb-4">Rekap Kehadiran</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-xs uppercase">Nama</th>
                        <th class="px-4 py-3 font-extrabold text-xs uppercase">NIP</th>
                        <th class="px-4 py-3 font-extrabold text-xs uppercase text-center">Hadir</th>
                        <th class="px-4 py-3 font-extrabold text-xs uppercase text-center">Terlambat</th>
                        <th class="px-4 py-3 font-extrabold text-xs uppercase text-center">Pulang Cepat</th>
                        <th class="px-4 py-3 font-extrabold text-xs uppercase text-center">Tidak Hadir</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($rekap as $userId => $records)
                    @php
                        $user = $records->first()->user;
                        $hadir = $records->where('status_check_in', 'tepat_waktu')->count();
                        $terlambat = $records->where('status_check_in', 'terlambat')->count();
                        $pulangCepat = $records->where('status_check_out', 'pulang_cepat')->count();
                        $totalHari = $records->count();
                    @endphp
                    <tr class="hover:bg-coral-bg/30">
                        <td class="px-4 py-3 font-bold text-sm">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->ptk?->nip ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                {{ $hadir }} hari
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                {{ $terlambat }} hari
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                {{ $pulangCepat }} hari
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                {{ 0 }} hari
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada data absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
