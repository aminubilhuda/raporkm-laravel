<?php

namespace App\Http\Controllers\Api\V1\Guru;

use App\Http\Controllers\Controller;
use App\Models\NilaiFormatif;
use App\Models\NilaiSumatifAs;
use App\Models\NilaiSumatifPh;
use App\Models\NilaiSumatifTs;
use App\Models\Sekolah;
use App\Models\SiswaKelas;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mapel_id' => ['required', 'exists:mapel,id'],
        ]);

        $kelasId = $request->integer('kelas_id');
        $mapelId = $request->integer('mapel_id');

        $tpList = TujuanPembelajaran::where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId)
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->orderBy('kode_tp')
            ->get();

        $siswaIds = SiswaKelas::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->pluck('siswa_id');

        $tpIds = $tpList->pluck('id');

        $nilaiFormatif = NilaiFormatif::whereIn('tujuan_pembelajaran_id', $tpIds)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->keyBy(fn ($n) => $n->siswa_id.'_'.$n->tujuan_pembelajaran_id);

        $nilaiSumatifPh = NilaiSumatifPh::whereIn('tujuan_pembelajaran_id', $tpIds)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->keyBy(fn ($n) => $n->siswa_id.'_'.$n->tujuan_pembelajaran_id);

        $nilaiSumatifAs = NilaiSumatifAs::where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId)
            ->whereIn('siswa_id', $siswaIds)
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->get()
            ->keyBy('siswa_id');

        $nilaiSumatifTs = NilaiSumatifTs::where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId)
            ->whereIn('siswa_id', $siswaIds)
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->get()
            ->keyBy('siswa_id');

        return response()->json([
            'success' => true,
            'data' => [
                'tp_list' => $tpList->map(fn ($tp) => [
                    'id' => $tp->id,
                    'kode_tp' => $tp->kode_tp,
                    'nama_tp' => $tp->nama_tp,
                ]),
                'nilai' => $siswaIds->map(function ($siswaId) use ($tpList, $nilaiFormatif, $nilaiSumatifPh, $nilaiSumatifAs, $nilaiSumatifTs) {
                    return [
                        'siswa_id' => $siswaId,
                        'formatif' => $tpList->mapWithKeys(fn ($tp) => [
                            $tp->kode_tp => $nilaiFormatif->get($siswaId.'_'.$tp->id)?->nilai,
                        ]),
                        'sumatif_ph' => $tpList->mapWithKeys(fn ($tp) => [
                            $tp->kode_tp => $nilaiSumatifPh->get($siswaId.'_'.$tp->id)?->nilai,
                        ]),
                        'sumatif_as' => $nilaiSumatifAs->get($siswaId)?->nilai,
                        'sumatif_ts' => $nilaiSumatifTs->get($siswaId)?->nilai,
                    ];
                }),
            ],
        ]);
    }

    public function storeFormatif(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswa,id'],
            'tujuan_pembelajaran_id' => ['required', 'exists:tujuan_pembelajaran,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mapel_id' => ['required', 'exists:mapel,id'],
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $sekolah = Sekolah::first();
        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $nilai = NilaiFormatif::updateOrCreate(
            [
                'siswa_id' => $validated['siswa_id'],
                'tujuan_pembelajaran_id' => $validated['tujuan_pembelajaran_id'],
                'tahun_pelajaran_id' => $validated['tahun_pelajaran_id'],
                'semester_id' => $validated['semester_id'],
            ],
            ['nilai' => $validated['nilai'], 'kelas_id' => $validated['kelas_id'], 'mapel_id' => $validated['mapel_id']],
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai formatif berhasil disimpan.',
            'data' => $nilai,
        ], 201);
    }

    public function storeSumatifPh(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswa,id'],
            'tujuan_pembelajaran_id' => ['required', 'exists:tujuan_pembelajaran,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mapel_id' => ['required', 'exists:mapel,id'],
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $sekolah = Sekolah::first();
        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $nilai = NilaiSumatifPh::updateOrCreate(
            [
                'siswa_id' => $validated['siswa_id'],
                'tujuan_pembelajaran_id' => $validated['tujuan_pembelajaran_id'],
                'tahun_pelajaran_id' => $validated['tahun_pelajaran_id'],
                'semester_id' => $validated['semester_id'],
            ],
            ['nilai' => $validated['nilai'], 'deskripsi' => $validated['deskripsi'] ?? null, 'kelas_id' => $validated['kelas_id'], 'mapel_id' => $validated['mapel_id']],
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai sumatif PH berhasil disimpan.',
            'data' => $nilai,
        ], 201);
    }

    public function storeSumatifAs(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswa,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mapel_id' => ['required', 'exists:mapel,id'],
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $sekolah = Sekolah::first();
        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $nilai = NilaiSumatifAs::updateOrCreate(
            [
                'siswa_id' => $validated['siswa_id'],
                'mapel_id' => $validated['mapel_id'],
                'kelas_id' => $validated['kelas_id'],
                'tahun_pelajaran_id' => $validated['tahun_pelajaran_id'],
                'semester_id' => $validated['semester_id'],
            ],
            ['nilai' => $validated['nilai'], 'deskripsi' => $validated['deskripsi'] ?? null],
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai sumatif AS berhasil disimpan.',
            'data' => $nilai,
        ], 201);
    }

    public function storeSumatifTs(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswa,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mapel_id' => ['required', 'exists:mapel,id'],
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $sekolah = Sekolah::first();
        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $nilai = NilaiSumatifTs::updateOrCreate(
            [
                'siswa_id' => $validated['siswa_id'],
                'mapel_id' => $validated['mapel_id'],
                'kelas_id' => $validated['kelas_id'],
                'tahun_pelajaran_id' => $validated['tahun_pelajaran_id'],
                'semester_id' => $validated['semester_id'],
            ],
            ['nilai' => $validated['nilai'], 'deskripsi' => $validated['deskripsi'] ?? null],
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai sumatif TS berhasil disimpan.',
            'data' => $nilai,
        ], 201);
    }
}
