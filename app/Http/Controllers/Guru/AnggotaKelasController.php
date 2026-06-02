<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\SiswaKelas;

class AnggotaKelasController extends Controller
{
    public function index(?Kelas $kelas = null)
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = $sekolah?->tahun_aktif;
        $semesterId = $sekolah?->semester_aktif;

        $kelasWaliIds = $user->kelasWali()
            ->when($taId, fn ($q) => $q->where('kelas_wali.tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('kelas_wali.semester_id', $semesterId))
            ->pluck('id');

        $mapelKelasIds = $user->mapelKelas()
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->pluck('kelas_id')
            ->unique();

        $allKelasIds = $kelasWaliIds->merge($mapelKelasIds)->unique();
        $daftarKelas = Kelas::whereIn('id', $allKelasIds)
            ->with('tingkat', 'kompetensiKeahlian')
            ->get();

        if (! $kelas || ! $kelas->exists) {
            $kelas = $daftarKelas->first();
        }

        $siswa = collect();
        if ($kelas) {
            $siswa = SiswaKelas::where('kelas_id', $kelas->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->with('siswa')
                ->get();
        }

        return view('guru.anggota-kelas.index', compact('daftarKelas', 'kelas', 'siswa'));
    }
}
