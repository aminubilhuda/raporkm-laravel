<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\JenisAbsen;
use App\Models\Presensi;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RekapPresensiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
        ]);

        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $presensi = Presensi::selectRaw('siswa_id, jenis_absen_id, count(*) as total')
            ->with(['siswa' => fn ($q) => $q->select('id', 'nama_siswa', 'nisn'), 'jenisAbsen' => fn ($q) => $q->select('id', 'nama')])
            ->where('kelas_id', $request->integer('kelas_id'))
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->groupBy('siswa_id', 'jenis_absen_id')
            ->get()
            ->groupBy('siswa_id');

        $rekap = $presensi->map(function ($items, $siswaId) {
            $siswa = $items->first()->siswa;
            $absensi = $items->mapWithKeys(fn ($item) => [
                $item->jenisAbsen?->nama => $item->total,
            ]);

            return [
                'siswa_id' => $siswaId,
                'nama_siswa' => $siswa->nama_siswa,
                'nisn' => $siswa->nisn,
                'absensi' => $absensi,
            ];
        })->values();

        $jenisAbsen = JenisAbsen::select('id', 'nama', 'keterangan')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'kelas_id' => $request->integer('kelas_id'),
                'jenis_absen' => $jenisAbsen,
                'rekap' => $rekap,
            ],
        ]);
    }

    public function detail(Request $request): JsonResponse
    {
        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'siswa_id' => ['required', 'exists:siswa,id'],
        ]);

        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $presensi = Presensi::with('jenisAbsen')
            ->where('kelas_id', $request->integer('kelas_id'))
            ->where('siswa_id', $request->integer('siswa_id'))
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->orderBy('tanggal')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'tanggal' => $p->tanggal,
                'jenis' => $p->jenisAbsen?->nama,
                'keterangan' => $p->keterangan,
            ]);

        return response()->json([
            'success' => true,
            'data' => $presensi,
        ]);
    }
}
