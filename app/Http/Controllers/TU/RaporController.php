<?php

namespace App\Http\Controllers\Tu;

use App\Http\Controllers\Controller;
use App\Models\KepalaSekolah;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Services\RaporService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function identitas(Siswa $siswa)
    {
        $sekolah = \App\Models\Sekolah::first();

        $kelasAktif = SiswaKelas::where('siswa_id', $siswa->id)
            ->where('status', 'aktif')
            ->with('kelas.kompetensiKeahlian')
            ->first();

        $kepalaSekolah = KepalaSekolah::where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
            ->first();

        $logoSekolah = $sekolah?->logo && Storage::disk('public')->exists($sekolah->logo)
            ? Storage::url($sekolah->logo)
            : '';

        $logoProv = $sekolah?->logo_prov && Storage::disk('public')->exists($sekolah->logo_prov)
            ? Storage::url($sekolah->logo_prov)
            : '';

        $formatRapor = $sekolah?->format_rapor ?? 'a4';

        $data = [
            'siswa' => $siswa,
            'sekolah' => $sekolah,
            'kompetensiKeahlian' => $kelasAktif?->kelas?->kompetensiKeahlian?->nama
                ?? $siswa->kompetensiKeahlian?->nama
                ?? '-',
            'kepalaSekolah' => $kepalaSekolah,
            'logoSekolah' => $logoSekolah,
            'logoProv' => $logoProv,
            'tanggalCetak' => now()->locale('id')->translatedFormat('j F Y'),
        ];

        $pdf = Pdf::loadView('tu.rapor.identitas-pdf', $data);

        if ($formatRapor === 'f4') {
            $pdf->setPaper([0, 0, 595.28, 935.43], 'portrait');
        } else {
            $pdf->setPaper('a4', 'portrait');
        }

        $filename = 'Identitas_Siswa_' . str_replace(' ', '_', $siswa->nama_siswa) . '.pdf';

        return $pdf->stream($filename);
    }
}
