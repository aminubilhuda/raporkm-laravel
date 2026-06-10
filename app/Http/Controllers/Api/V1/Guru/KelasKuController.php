<?php

namespace App\Http\Controllers\Api\V1\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KelasKuController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $kelasIds = $user->mapelKelas()
            ->when($tpId, fn ($q) => $q->where('mapel_kelas.tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('mapel_kelas.semester_id', $semId))
            ->pluck('mapel_kelas.kelas_id')
            ->unique();

        $waliIds = $user->kelasWali()
            ->when($tpId, fn ($q) => $q->where('kelas_wali.tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('kelas_wali.semester_id', $semId))
            ->pluck('kelas_wali.kelas_id');

        $allKelasIds = $kelasIds->merge($waliIds)->unique();

        $kelas = Kelas::with(['tingkat', 'kompetensiKeahlian'])
            ->withCount('siswaKelas')
            ->whereIn('id', $allKelasIds)
            ->orderBy('nama_kelas')
            ->get();

        $mapelKelas = $user->mapelKelas()
            ->with('mapel')
            ->when($tpId, fn ($q) => $q->where('mapel_kelas.tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('mapel_kelas.semester_id', $semId))
            ->get()
            ->groupBy('kelas_id');

        $waliKelasIds = $waliIds->toArray();

        return response()->json([
            'success' => true,
            'data' => $kelas->map(fn ($k) => [
                'id' => $k->id,
                'nama_kelas' => $k->nama_kelas,
                'tingkat' => $k->tingkat?->nama,
                'jurusan' => $k->kompetensiKeahlian?->nama,
                'jumlah_siswa' => $k->siswa_kelas_count,
                'is_wali' => in_array($k->id, $waliKelasIds),
                'mapel' => ($mapelKelas->get($k->id) ?? collect())->map(fn ($mk) => [
                    'id' => $mk->mapel?->id,
                    'nama_mapel' => $mk->mapel?->nama_mapel,
                    'kode' => $mk->mapel?->kode,
                ]),
            ]),
        ]);
    }

    public function siswa(Request $request, string $kelasId): JsonResponse
    {
        $user = $request->user();
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $kelas = Kelas::with(['tingkat', 'kompetensiKeahlian'])->find($kelasId);

        if (! $kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan.'], 404);
        }

        $siswa = $kelas->siswaKelas()
            ->with('siswa')
            ->where('siswa_kelas.status', 'aktif')
            ->when($tpId, fn ($q) => $q->where('siswa_kelas.tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('siswa_kelas.semester_id', $semId))
            ->get()
            ->map(fn ($sk) => [
                'id' => $sk->siswa?->id,
                'nama_siswa' => $sk->siswa?->nama_siswa,
                'nisn' => $sk->siswa?->nisn,
                'kelamin' => $sk->siswa?->kelamin,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'kelas' => [
                    'id' => $kelas->id,
                    'nama_kelas' => $kelas->nama_kelas,
                    'tingkat' => $kelas->tingkat?->nama,
                    'jurusan' => $kelas->kompetensiKeahlian?->nama,
                ],
                'siswa' => $siswa,
            ],
        ]);
    }
}
