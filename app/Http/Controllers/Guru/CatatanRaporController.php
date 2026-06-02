<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreCatatanRaporRequest;
use App\Models\CatatanWali;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\SiswaKelas;

class CatatanRaporController extends Controller
{
    public function index(?Kelas $kelas = null)
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

        $authorized = $kelas && $kelasWali->contains(fn ($k) => $k->id === $kelas->id);

        $siswa = collect();
        $catatan = collect();

        if ($authorized) {
            $siswa = SiswaKelas::where('kelas_id', $kelas->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->with('siswa')
                ->get();

            $catatan = CatatanWali::where('kelas_id', $kelas->id)
                ->where('user_id', $user->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->get()
                ->keyBy('siswa_id');
        }

        return view('guru.catatan-rapor.index', compact('kelasWali', 'kelas', 'authorized', 'siswa', 'catatan'));
    }

    public function store(StoreCatatanRaporRequest $request)
    {
        $user = $request->user();
        $sekolah = Sekolah::first();

        abort_unless(
            $user->kelasWali()
                ->where('kelas_wali.tahun_pelajaran_id', $sekolah?->tahun_aktif)
                ->where('kelas_wali.semester_id', $sekolah?->semester_aktif)
                ->where('kelas_wali.kelas_id', $request->kelas_id)->exists(),
            403
        );

        CatatanWali::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'kelas_id' => $request->kelas_id,
                'user_id' => $user->id,
                'tahun_pelajaran_id' => $sekolah?->tahun_aktif,
                'semester_id' => $sekolah?->semester_aktif,
            ],
            ['catatan' => $request->catatan]
        );

        return redirect()->route('guru.catatan-rapor.index', $request->kelas_id)
            ->with('status', 'Catatan rapor berhasil disimpan.');
    }
}
