<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;

class KelasKuController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        $kelasWali = $user->kelasWali()
            ->when($taId, fn ($q) => $q->where('kelas_wali.tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('kelas_wali.semester_id', $semesterId))
            ->with('tingkat', 'kompetensiKeahlian')
            ->get();

        $mapelKelas = $user->mapelKelas()
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->with('mapel', 'kelas.tingkat', 'kelas.kompetensiKeahlian')
            ->get()
            ->sortBy(fn ($item) => $item->mapel?->urutan ?? 0)
            ->groupBy(fn ($item) => $item->kelas->nama_kelas ?? 'Tanpa Kelas');

        return view('guru.kelas-ku.index', compact('kelasWali', 'mapelKelas'));
    }
}
