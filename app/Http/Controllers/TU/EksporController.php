<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Services\ExportService;
use Illuminate\Http\Request;

class EksporController extends Controller
{
    public function __construct(private ExportService $ekspor) {}

    public function index()
    {
        $kelasList = Kelas::with('tingkat', 'kompetensiKeahlian')->orderBy('nama_kelas')->get();
        $mapelList = Mapel::orderBy('urutan')->get();
        $tahunList = TahunPelajaran::orderByDesc('status')->orderByDesc('tahun')->get();
        $semesterList = Semester::orderBy('urutan')->get();

        return view('tu.ekspor.index', compact('kelasList', 'mapelList', 'tahunList', 'semesterList'));
    }

    public function nilai(Request $request)
    {
        $data = $request->validate([
            'kelas_id' => 'required|integer|exists:kelas,id',
            'mapel_id' => 'required|integer|exists:mapel,id',
            'tahun' => 'required|integer|exists:tahun_pelajaran,id',
            'semester' => 'required|integer|exists:semester,id',
        ]);

        return $this->ekspor->exportNilai(
            $data['kelas_id'],
            $data['mapel_id'],
            $data['tahun'],
            $data['semester']
        );
    }

    public function presensi(Request $request)
    {
        $data = $request->validate([
            'kelas_id' => 'required|integer|exists:kelas,id',
            'tahun' => 'required|integer|exists:tahun_pelajaran,id',
            'semester' => 'required|integer|exists:semester,id',
        ]);

        return $this->ekspor->exportPresensi($data['kelas_id'], $data['tahun'], $data['semester']);
    }

    public function siswa(Request $request)
    {
        $data = $request->validate([
            'kelas_id' => 'nullable|integer|exists:kelas,id',
        ]);

        return $this->ekspor->exportSiswa($data['kelas_id'] ?? null);
    }
}
