<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\CatatanWali;
use App\Models\PembagianRaport;
use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\SiswaKelas;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

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

        // Widget: Daftar Kelas yang Diajar
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
            });

        // Widget: Progress Input Nilai
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

        // Widget: Presensi Hari Ini
        $presensiHariIni = Presensi::whereDate('created_at', now()->toDateString())
            ->whereIn('kelas_id', $allKelasIds)
            ->selectRaw('jenis_absen_id, count(*) as total')
            ->groupBy('jenis_absen_id')
            ->pluck('total', 'jenis_absen_id');

        // Widget: Catatan Rapor Pending
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

        return view('guru.dashboard', [
            'totalKelasWali' => $kelasWali->count(),
            'totalMapelDiajar' => $mapelDiajar->count(),
            'totalSiswa' => $totalSiswa,
            'siswaWali' => $siswaWali,
            'kelasWaliList' => $kelasWali,
            'mapelDiajar' => $mapelDiajar,
            'pembagian' => $pembagian,
            'kelasYangDiajar' => $kelasYangDiajar,
            'progressNilai' => $progressNilai,
            'totalTp' => $totalTp,
            'tpDenganNilai' => $tpDenganNilai,
            'presensiHariIni' => $presensiHariIni,
            'catatanPending' => $catatanPending,
        ]);
    }

    public function setSemester(Request $request)
    {
        $request->validate([
            'tahun_pelajaran_id' => ['required', 'exists:tahun_pelajaran,id'],
            'semester_id' => ['required', 'exists:semester,id'],
        ]);

        session([
            'selected_tahun' => $request->integer('tahun_pelajaran_id'),
            'selected_semester' => $request->integer('semester_id'),
        ]);

        return back();
    }
}
