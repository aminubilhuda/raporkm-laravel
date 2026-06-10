<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\PembagianRaport;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semesterId = $sekolah?->semester_aktif;

        $tp = TahunPelajaran::find($tpId);
        $semester = Semester::find($semesterId);

        $totalSiswa = Siswa::where('aktif', 1)->count();
        $totalLaki = Siswa::where('aktif', 1)->where('kelamin', 1)->count();
        $totalPerempuan = $totalSiswa - $totalLaki;

        $pembagian = PembagianRaport::where('tahun_pelajaran_id', $tpId)
            ->where('semester_id', $semesterId)
            ->first();

        $hariTersisa = null;
        $progress = 0;
        if ($pembagian?->tanggal_semester) {
            $hariTersisa = max(0, (int) now()->diffInDays($pembagian->tanggal_semester, false));
        }
        if ($pembagian?->tanggal_mid && $pembagian?->tanggal_semester) {
            $totalHari = $pembagian->tanggal_mid->diffInDays($pembagian->tanggal_semester);
            $hariBerlalu = max(0, $pembagian->tanggal_mid->diffInDays(now()));
            $progress = $totalHari > 0 ? min(100, round($hariBerlalu / $totalHari * 100)) : 0;
        }

        $siswaPerTingkat = SiswaKelas::where('siswa_kelas.tahun_pelajaran_id', $tpId)
            ->where('siswa_kelas.semester_id', $semesterId)
            ->where('siswa_kelas.status', 'aktif')
            ->join('kelas', 'siswa_kelas.kelas_id', '=', 'kelas.id')
            ->join('tingkat', 'kelas.tingkat_id', '=', 'tingkat.id')
            ->select('tingkat.nama as nama_tingkat', DB::raw('count(*) as total'))
            ->groupBy('tingkat.nama', 'tingkat.urutan')
            ->orderBy('tingkat.urutan')
            ->get();

        $mapelPerKelompok = Mapel::join('kelompok_mapel', 'mapel.kelompok_mapel_id', '=', 'kelompok_mapel.id')
            ->select('kelompok_mapel.nama as nama_kelompok', DB::raw('count(*) as total'))
            ->groupBy('kelompok_mapel.nama')
            ->orderBy('nama_kelompok')
            ->get();

        $totalGuru = User::where('jabatan', 3)->count();
        $guruAktif = Activity::where('causer_type', User::class)
            ->where('event', 'created')
            ->where('created_at', '>=', now()->startOfMonth())
            ->distinct('causer_id')
            ->count('causer_id');

        $recentActivity = Activity::with('causer')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'event' => $a->event,
                'description' => $a->description,
                'causer' => $a->causer?->nama,
                'created_at' => $a->created_at->toISOString(),
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'total_siswa' => $totalSiswa,
                'total_kelas' => Kelas::count(),
                'total_mapel' => Mapel::count(),
                'total_guru' => $totalGuru,
                'siswa_laki' => $totalLaki,
                'siswa_perempuan' => $totalPerempuan,
                'tahun_aktif' => $tp?->tahun,
                'semester_aktif' => $semester?->nama,
                'hari_tersisa' => $hariTersisa,
                'progress_semester' => $progress,
                'guru_aktif_bulan_ini' => $guruAktif,
                'siswa_per_tingkat' => $siswaPerTingkat,
                'mapel_per_kelompok' => $mapelPerKelompok,
                'recent_activity' => $recentActivity,
            ],
        ]);
    }
}
