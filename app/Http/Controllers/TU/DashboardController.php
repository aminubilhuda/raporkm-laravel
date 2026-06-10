<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\PembagianRaport;
use App\Models\Pengingat;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $sekolah = Sekolah::first();
        $tpId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        $tp = TahunPelajaran::find($tpId);
        $semester = Semester::find($semesterId);

        $totalSiswa = Siswa::where('aktif', 1)->count();
        $totalLaki = Siswa::where('aktif', 1)->where('kelamin', 1)->count();
        $totalPerempuan = Siswa::where('aktif', 1)->where('kelamin', 2)->count();

        $persenLaki = $totalSiswa > 0 ? round($totalLaki / $totalSiswa * 100) : 0;
        $persenPerempuan = 100 - $persenLaki;

        $pembagian = PembagianRaport::where('tahun_pelajaran_id', $tpId)
            ->where('semester_id', $semesterId)
            ->first();

        $hariTersisa = '-';
        $progress = 0;
        if ($pembagian?->tanggal_semester) {
            $hariTersisa = max(0, (int) now()->diffInDays($pembagian->tanggal_semester, false));
        }
        if ($pembagian?->tanggal_mid && $pembagian?->tanggal_semester) {
            $totalHari = $pembagian->tanggal_mid->diffInDays($pembagian->tanggal_semester);
            $hariBerlalu = max(0, $pembagian->tanggal_mid->diffInDays(now()));
            $progress = $totalHari > 0 ? min(100, round($hariBerlalu / $totalHari * 100)) : 0;
        }

        // Widget 1: Aktivitas Terbaru
        $aktivitas = Activity::with('causer')
            ->latest()
            ->take(8)
            ->get();

        // Widget 2: Siswa Per Tingkat
        $siswaPerTingkat = SiswaKelas::where('siswa_kelas.tahun_pelajaran_id', $tpId)
            ->where('siswa_kelas.semester_id', $semesterId)
            ->where('siswa_kelas.status', 'aktif')
            ->join('kelas', 'siswa_kelas.kelas_id', '=', 'kelas.id')
            ->join('tingkat', 'kelas.tingkat_id', '=', 'tingkat.id')
            ->select('tingkat.nama as nama_tingkat', DB::raw('count(*) as total'))
            ->groupBy('tingkat.nama', 'tingkat.urutan')
            ->orderBy('tingkat.urutan')
            ->get();

        // Widget 3: Mapel Per Kelompok
        $mapelPerKelompok = Mapel::join('kelompok_mapel', 'mapel.kelompok_mapel_id', '=', 'kelompok_mapel.id')
            ->select('kelompok_mapel.nama as nama_kelompok', DB::raw('count(*) as total'))
            ->groupBy('kelompok_mapel.nama')
            ->orderBy('nama_kelompok')
            ->get();

        // Widget 4: Guru Aktif (input nilai bulan ini)
        $totalGuru = User::where('jabatan', 3)->count();
        $guruAktif = Activity::where('causer_type', User::class)
            ->where('event', 'created')
            ->where('created_at', '>=', now()->startOfMonth())
            ->distinct('causer_id')
            ->count('causer_id');

        // Widget 5: Pengingat
        $pengingats = Pengingat::latest()->take(5)->get();

        return view('tu.dashboard', [
            'totalSiswa' => $totalSiswa,
            'totalKelas' => Kelas::count(),
            'totalMapel' => Mapel::count(),
            'totalGuru' => $totalGuru,
            'tahunLabel' => $tp?->tahun ?? '-',
            'semesterLabel' => $semester?->nama ?? '-',
            'pembagian' => $pembagian,
            'totalLaki' => $totalLaki,
            'totalPerempuan' => $totalPerempuan,
            'persenLaki' => $persenLaki,
            'persenPerempuan' => $persenPerempuan,
            'hariTersisa' => $hariTersisa,
            'progress' => $progress,
            'aktivitas' => $aktivitas,
            'siswaPerTingkat' => $siswaPerTingkat,
            'mapelPerKelompok' => $mapelPerKelompok,
            'guruAktif' => $guruAktif,
            'pengingats' => $pengingats,
        ]);
    }
}
