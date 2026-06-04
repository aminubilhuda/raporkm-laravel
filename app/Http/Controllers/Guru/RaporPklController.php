<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\SiswaPrakerin;
use App\Services\RaporService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RaporPklController extends Controller
{
    public function __construct(private RaporService $rapor) {}

    public function index(Request $r)
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = $sekolah?->tahun_aktif;
        $semesterId = $sekolah?->semester_aktif;

        $daftarPkl = SiswaPrakerin::with(['siswa', 'kelas.tingkat', 'kelas.kompetensiKeahlian', 'prakerin'])
            ->where('user_id', $user->id)
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->latest()
            ->get();

        return view('guru.rapor-pkl.index', compact('daftarPkl'));
    }

    public function pdf(SiswaPrakerin $siswaPrakerin)
    {
        abort_unless($siswaPrakerin->user_id === auth()->id(), 403);

        $data = $this->rapor->getDataRaporPkl($siswaPrakerin->id);

        $pdf = Pdf::loadView('tu.rapor.pkl-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Rapor-PKL-'.$data['siswa']->nama_siswa.'.pdf';

        return $pdf->stream($filename);
    }
}
