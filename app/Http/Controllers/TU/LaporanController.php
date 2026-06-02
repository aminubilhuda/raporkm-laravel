<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\LaporanWa;
use App\Services\LaporanPendidikanService;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(private LaporanPendidikanService $pendidikan)
    {
    }

    public function index()
    {
        $laporanWa = LaporanWa::latest()->paginate(15);

        return view('tu.laporan.index', compact('laporanWa'));
    }

    public function pendidikan(Request $request)
    {
        $sekolah = \App\Models\Sekolah::first();
        $tahunId = $request->integer('tahun') ?: ($sekolah?->tahun_aktif);
        $semesterId = $request->integer('semester') ?: ($sekolah?->semester_aktif);

        $data = $tahunId && $semesterId
            ? $this->pendidikan->aggregate($tahunId, $semesterId)
            : ['kelas_list' => collect(), 'mapel_list' => collect(), 'nilai_per_mapel' => [], 'distribusi_predikat' => [], 'top_bottom' => ['top' => [], 'bottom' => []], 'presensi_rekap' => ['sakit' => 0, 'izin' => 0, 'alpha' => 0, 'hadir' => 0]];

        $tahunList = \App\Models\TahunPelajaran::orderByDesc('tahun')->get();
        $semesterList = \App\Models\Semester::all();

        return view('tu.laporan.pendidikan', array_merge($data, compact('tahunList', 'semesterList', 'tahunId', 'semesterId')));
    }
}
