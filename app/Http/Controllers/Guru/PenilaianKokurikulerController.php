<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StorePenilaianKokurikulerRequest;
use App\Models\DimensiKokurikuler;
use App\Models\Kelas;
use App\Models\NilaiKokurikuler;
use App\Models\Sekolah;
use App\Models\SiswaKelas;

class PenilaianKokurikulerController extends Controller
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

        $authorized = $kelas && $kelasWali->contains('id', $kelas->id);

        $siswa = collect();
        $dimensiList = collect();
        $nilai = collect();

        if ($authorized) {
            $siswa = SiswaKelas::where('kelas_id', $kelas->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->with('siswa')
                ->get();

            $dimensiList = DimensiKokurikuler::get();

            $nilai = NilaiKokurikuler::where('kelas_id', $kelas->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->get()
                ->keyBy(fn ($n) => "{$n->siswa_id}_{$n->dimensi_kokurikuler_id}");
        }

        return view('guru.penilaian-kokurikuler.index', compact('kelasWali', 'kelas', 'authorized', 'siswa', 'dimensiList', 'nilai'));
    }

    public function store(StorePenilaianKokurikulerRequest $request)
    {
        $sekolah = Sekolah::first();

        abort_unless($this->isWali($request->kelas_id), 403);

        foreach ($request->siswa_id as $siswaId) {
            NilaiKokurikuler::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'kelas_id' => $request->kelas_id,
                    'dimensi_kokurikuler_id' => $request->dimensi_id,
                ],
                [
                    'nilai' => $request->input("nilai.{$siswaId}"),
                    'tahun_pelajaran_id' => $sekolah?->tahun_aktif,
                    'semester_id' => $sekolah?->semester_aktif,
                ]
            );
        }

        return redirect()->route('guru.penilaian-kokurikuler.index', $request->kelas_id)
            ->with('status', 'Nilai kokurikuler berhasil disimpan.');
    }

    private function isWali(int $kelasId): bool
    {
        $sekolah = Sekolah::first();

        return auth()->user()->kelasWali()
            ->where('kelas_wali.tahun_pelajaran_id', $sekolah?->tahun_aktif)
            ->where('kelas_wali.semester_id', $sekolah?->semester_aktif)
            ->where('kelas_wali.kelas_id', $kelasId)->exists();
    }
}
