<?php

namespace App\Http\Controllers\Api\V1\Guru;

use App\Http\Controllers\Controller;
use App\Models\CatatanWali;
use App\Models\JenisAbsen;
use App\Models\PembagianRaport;
use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\SiswaKelas;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
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
            ->get()
            ->sortBy(fn ($mk) => $mk->mapel?->urutan ?? 0);

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

        $pembagian = PembagianRaport::where('tahun_pelajaran_id', $taId)
            ->where('semester_id', $semesterId)
            ->first();

        $kelasYangDiajar = $mapelDiajar->groupBy(fn ($mk) => $mk->kelas?->nama_kelas ?? 'Tanpa Kelas')
            ->map(function ($items, $namaKelas) {
                $kelasId = $items->first()->kelas_id;
                $siswaCount = SiswaKelas::where('kelas_id', $kelasId)
                    ->where('status', 'aktif')
                    ->count();

                return [
                    'nama_kelas' => $namaKelas,
                    'kelas_id' => $kelasId,
                    'jumlah_mapel' => $items->count(),
                    'jumlah_siswa' => $siswaCount,
                    'mapel_list' => $items->pluck('mapel.nama_mapel')->implode(', '),
                ];
            })
            ->values();

        $kelasIds = $mapelDiajar->pluck('kelas_id')->unique();
        $totalTp = TujuanPembelajaran::whereIn('kelas_id', $kelasIds)
            ->where('tahun_pelajaran_id', $taId)
            ->where('semester_id', $semesterId)
            ->count();
        $tpDenganNilai = TujuanPembelajaran::whereIn('kelas_id', $kelasIds)
            ->where('tahun_pelajaran_id', $taId)
            ->where('semester_id', $semesterId)
            ->whereHas('nilaiFormatif')
            ->count();
        $progressNilai = $totalTp > 0 ? round($tpDenganNilai / $totalTp * 100) : 0;

        $presensiHariIni = JenisAbsen::all()
            ->mapWithKeys(function ($ja) use ($allKelasIds) {
                $count = Presensi::whereDate('created_at', now()->toDateString())
                    ->whereIn('kelas_id', $allKelasIds)
                    ->where('jenis_absen_id', $ja->id)
                    ->count();

                return [$ja->nama => $count];
            })
            ->filter(fn ($count) => $count > 0);

        $siswaIds = SiswaKelas::whereIn('kelas_id', $kelasWaliIds)
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->where('status', 'aktif')
            ->pluck('siswa_id');

        $catatanPending = $siswaIds->isNotEmpty()
            ? CatatanWali::where('tahun_pelajaran_id', $taId)
                ->where('semester_id', $semesterId)
                ->whereIn('siswa_id', $siswaIds)
                ->whereNull('catatan')
                ->count()
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_kelas_wali' => $kelasWali->count(),
                'total_mapel_diajar' => $mapelDiajar->count(),
                'total_siswa' => $totalSiswa,
                'siswa_wali' => $siswaWali,
                'kelas_wali' => $kelasWali->map(fn ($k) => [
                    'id' => $k->id,
                    'nama_kelas' => $k->nama_kelas,
                    'tingkat' => $k->tingkat?->nama,
                    'jurusan' => $k->kompetensiKeahlian?->nama,
                ]),
                'kelas_yang_diajar' => $kelasYangDiajar,
                'progress_nilai' => $progressNilai,
                'total_tp' => $totalTp,
                'tp_dengan_nilai' => $tpDenganNilai,
                'presensi_hari_ini' => $presensiHariIni,
                'catatan_pending' => $catatanPending,
                'tahun_aktif' => $sekolah?->tahunPelajaran?->tahun,
                'semester_aktif' => $sekolah?->semester?->nama,
            ],
        ]);
    }
}
