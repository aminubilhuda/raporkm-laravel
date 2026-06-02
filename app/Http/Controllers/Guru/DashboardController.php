<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\SiswaKelas;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = $sekolah?->tahun_aktif;
        $semesterId = $sekolah?->semester_aktif;

        $kelasWali = $user->kelasWali()
            ->when($taId, fn ($q) => $q->where('kelas_wali.tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('kelas_wali.semester_id', $semesterId))
            ->with('tingkat', 'kompetensiKeahlian')
            ->get();

        $mapelDiajar = $user->mapelKelas()
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->with('mapel', 'kelas')
            ->get();

        $kelasWaliIds = $kelasWali->pluck('id');
        $mapelKelasIds = $mapelDiajar->pluck('kelas_id')->unique();
        $allKelasIds = $kelasWaliIds->merge($mapelKelasIds)->unique();

        $totalSiswa = SiswaKelas::whereIn('kelas_id', $allKelasIds)
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->count();

        $siswaWali = $kelasWaliIds->isNotEmpty()
            ? SiswaKelas::whereIn('kelas_id', $kelasWaliIds)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->count()
            : 0;

        return view('guru.dashboard', [
            'totalKelasWali' => $kelasWali->count(),
            'totalMapelDiajar' => $mapelDiajar->count(),
            'totalSiswa' => $totalSiswa,
            'siswaWali' => $siswaWali,
            'kelasWaliList' => $kelasWali,
            'mapelDiajar' => $mapelDiajar,
        ]);
    }
}
