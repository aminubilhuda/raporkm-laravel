<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JenisAbsen;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;

class AbsensiBkController extends Controller
{
    public function index(Request $r)
    {
        $kelass = Kelas::orderBy('nama_kelas')->get();
        $kelasId = $r->get('kelas_id');
        $tanggal = $r->get('tanggal', now()->format('Y-m-d'));
        $jenisAbsens = JenisAbsen::all();
        $siswa = collect();
        $presensiHariIni = collect();

        if ($kelasId) {
            $sekolah = Sekolah::first();
            $taId = $sekolah?->tahun_aktif;
            $semesterId = $sekolah?->semester_aktif;

            $siswa = SiswaKelas::where('kelas_id', $kelasId)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->with('siswa')
                ->get();

            $presensiHariIni = Presensi::where('kelas_id', $kelasId)
                ->where('tanggal', $tanggal)
                ->where('keterangan', 'BK')
                ->get()
                ->keyBy('siswa_id');
        }

        return view('guru.absensi-bk.index', compact('kelass', 'kelasId', 'tanggal', 'jenisAbsens', 'siswa', 'presensiHariIni'));
    }

    public function store(Request $r)
    {
        $sekolah = Sekolah::first();
        $taId = $sekolah?->tahun_aktif;
        $semesterId = $sekolah?->semester_aktif;

        $validated = $r->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'siswa_id' => 'required|array',
            'siswa_id.*' => 'exists:siswa,id',
            'jenis_absen_id' => 'required|array',
            'jenis_absen_id.*' => 'exists:jenis_absen,id',
        ]);

        foreach ($validated['siswa_id'] as $siswaId) {
            $jenisAbsenId = $validated['jenis_absen_id'][$siswaId] ?? 1;

            Presensi::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'kelas_id' => $validated['kelas_id'],
                    'tanggal' => $validated['tanggal'],
                    'keterangan' => 'BK',
                ],
                [
                    'jenis_absen_id' => $jenisAbsenId,
                    'tahun_pelajaran_id' => $taId,
                    'semester_id' => $semesterId,
                ]
            );
        }

        return redirect()->route('guru.absensi-bk.index', [
            'kelas_id' => $validated['kelas_id'],
            'tanggal' => $validated['tanggal'],
        ])->with('status', 'Absensi BK berhasil disimpan.');
    }
}
