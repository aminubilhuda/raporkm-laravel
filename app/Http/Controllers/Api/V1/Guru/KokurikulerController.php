<?php

namespace App\Http\Controllers\Api\V1\Guru;

use App\Http\Controllers\Controller;
use App\Models\DimensiKokurikuler;
use App\Models\NilaiKokurikuler;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KokurikulerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
        ]);

        $dimensi = DimensiKokurikuler::with('deskripsiKokurikuler')->get();

        $nilai = NilaiKokurikuler::where('kelas_id', $request->integer('kelas_id'))
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->get()
            ->keyBy(fn ($n) => $n->siswa_id.'_'.$n->dimensi_kokurikuler_id);

        $siswaIds = SiswaKelas::where('kelas_id', $request->integer('kelas_id'))
            ->where('status', 'aktif')
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->pluck('siswa_id');

        $siswa = Siswa::whereIn('id', $siswaIds)->select('id', 'nama_siswa')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'dimensi' => $dimensi->map(fn ($d) => [
                    'id' => $d->id,
                    'nama' => $d->nama,
                    'deskripsi' => $d->deskripsiKokurikuler->map(fn ($dk) => [
                        'id' => $dk->id,
                        'predikat' => $dk->predikat,
                        'deskripsi' => $dk->deskripsi,
                    ]),
                ]),
                'siswa' => $siswa->map(fn ($s) => [
                    'id' => $s->id,
                    'nama_siswa' => $s->nama_siswa,
                    'nilai' => $dimensi->mapWithKeys(fn ($d) => [
                        $d->nama => $nilai->get($s->id.'_'.$d->id)?->nilai,
                    ]),
                ]),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();

        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswa,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'dimensi_kokurikuler_id' => ['required', 'exists:dimensi_kokurikuler,id'],
            'nilai' => ['required', 'string', 'max:30'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $nilai = NilaiKokurikuler::updateOrCreate(
            [
                'siswa_id' => $validated['siswa_id'],
                'kelas_id' => $validated['kelas_id'],
                'dimensi_kokurikuler_id' => $validated['dimensi_kokurikuler_id'],
                'tahun_pelajaran_id' => $validated['tahun_pelajaran_id'],
                'semester_id' => $validated['semester_id'],
            ],
            ['nilai' => $validated['nilai'], 'deskripsi' => $validated['deskripsi'] ?? null],
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai kokurikuler berhasil disimpan.',
            'data' => $nilai,
        ], 201);
    }
}
