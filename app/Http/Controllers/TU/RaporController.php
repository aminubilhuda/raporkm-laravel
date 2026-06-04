<?php

namespace App\Http\Controllers\Tu;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Services\RaporService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RaporController extends Controller
{
    public function __construct(private RaporService $rapor) {}

    public function pilih()
    {
        $siswaList = SiswaKelas::with('siswa', 'kelas.tingkat')
            ->where('status', 'aktif')
            ->orderBy('kelas_id')
            ->get()
            ->pluck('siswa')
            ->unique('id')
            ->values();

        return view('tu.rapor.pilih', compact('siswaList'));
    }

    public function semester(Request $request, Siswa $siswa, int $tahun, int $semester)
    {
        $data = $this->rapor->getDataRaporSemester($siswa->id, $tahun, $semester);

        $pdf = Pdf::loadView('tu.rapor.semester-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Rapor-Semester-'.$siswa->nama_siswa.'.pdf';

        return $pdf->stream($filename);
    }

    public function mid(Request $request, Siswa $siswa, int $tahun, int $semester)
    {
        $data = $this->rapor->getDataRaporMid($siswa->id, $tahun, $semester);

        $pdf = Pdf::loadView('tu.rapor.mid-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Rapor-Mid-'.$siswa->nama_siswa.'.pdf';

        return $pdf->stream($filename);
    }

    public function pkl(Request $request, int $siswaPrakerin)
    {
        $data = $this->rapor->getDataRaporPkl($siswaPrakerin);

        $pdf = Pdf::loadView('tu.rapor.pkl-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Rapor-PKL-'.$data['siswa']->nama_siswa.'.pdf';

        return $pdf->stream($filename);
    }
}
