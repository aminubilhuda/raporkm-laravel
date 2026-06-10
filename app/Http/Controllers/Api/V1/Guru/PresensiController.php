<?php

namespace App\Http\Controllers\Api\V1\Guru;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\SiswaKelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresensiController extends Controller
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

        $kelasId = $request->integer('kelas_id');

        $siswaIds = SiswaKelas::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->pluck('siswa_id');

        $presensi = Presensi::with(['siswa', 'jenisAbsen'])
            ->where('kelas_id', $kelasId)
            ->whereIn('siswa_id', $siswaIds)
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->orderBy('tanggal')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $presensi->map(fn ($p) => [
                'id' => $p->id,
                'siswa_id' => $p->siswa_id,
                'nama_siswa' => $p->siswa?->nama_siswa,
                'tanggal' => $p->tanggal,
                'jenis_absen' => $p->jenisAbsen?->nama,
                'keterangan' => $p->keterangan,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $sekolah = Sekolah::first();

        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswa,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'jenis_absen_id' => ['required', 'exists:jenis_absen,id'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $presensi = Presensi::updateOrCreate(
            [
                'siswa_id' => $validated['siswa_id'],
                'kelas_id' => $validated['kelas_id'],
                'tanggal' => $validated['tanggal'],
                'tahun_pelajaran_id' => $validated['tahun_pelajaran_id'],
                'semester_id' => $validated['semester_id'],
            ],
            [
                'jenis_absen_id' => $validated['jenis_absen_id'],
                'keterangan' => $validated['keterangan'] ?? null,
            ],
        );

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil disimpan.',
            'data' => $presensi,
        ], 201);
    }

    public function rekap(Request $request): JsonResponse
    {
        $user = $request->user();
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
        ]);

        $rekap = Presensi::selectRaw('siswa_id, jenis_absen_id, count(*) as total')
            ->with(['siswa' => fn ($q) => $q->select('id', 'nama_siswa'), 'jenisAbsen' => fn ($q) => $q->select('id', 'nama')])
            ->where('kelas_id', $request->integer('kelas_id'))
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->groupBy('siswa_id', 'jenis_absen_id')
            ->get()
            ->groupBy('siswa_id')
            ->map(function ($items) {
                $siswa = $items->first()->siswa;
                $absensi = $items->mapWithKeys(fn ($item) => [$item->jenisAbsen?->nama => $item->total]);

                return [
                    'siswa_id' => $items->first()->siswa_id,
                    'nama_siswa' => $siswa->nama_siswa,
                    'absensi' => $absensi,
                ];
            })->values();

        return response()->json([
            'success' => true,
            'data' => $rekap,
        ]);
    }
}
