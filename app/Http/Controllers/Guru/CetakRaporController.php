<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Services\RaporService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use ZipArchive;

class CetakRaporController extends Controller
{
    public function __construct(private RaporService $rapor) {}

    public function index(?Kelas $kelas = null)
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        // Get kelas where user is wali
        $kelasWali = $user->kelasWali()
            ->when($taId, fn ($q) => $q->where('kelas_wali.tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('kelas_wali.semester_id', $semesterId))
            ->with('tingkat', 'kompetensiKeahlian')
            ->get();

        $authorized = $kelas && $kelasWali->contains('id', $kelas->id);

        $siswa = collect();
        if ($authorized) {
            $siswa = SiswaKelas::where('kelas_id', $kelas->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->with('siswa')
                ->get();
        }

        return view('guru.cetak-rapor.index', compact('kelasWali', 'kelas', 'authorized', 'siswa'));
    }

    public function cetak(Request $request, Kelas $kelas)
    {
        abort_unless($this->isWali($kelas->id), 403);

        $request->validate([
            'siswa_id' => 'required|array|min:1',
            'siswa_id.*' => 'exists:siswa,id',
            'jenis' => 'required|in:semester,mid',
        ]);

        $sekolah = Sekolah::first();
        $taId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        $siswaIds = $request->siswa_id;

        // If only 1 student selected, download single PDF
        if (count($siswaIds) === 1) {
            return $this->generateSinglePdf($siswaIds[0], $request->jenis, $taId, $semesterId);
        }

        // If multiple students selected, generate ZIP containing all PDFs
        return $this->generateZipBatch($siswaIds, $request->jenis, $taId, $semesterId, $kelas);
    }

    private function generateSinglePdf(int $siswaId, string $jenis, int $taId, int $semesterId)
    {
        $siswa = Siswa::findOrFail($siswaId);

        if ($jenis === 'mid') {
            $data = $this->rapor->getDataRaporMid($siswaId, $taId, $semesterId);
            $pdf = Pdf::loadView('tu.rapor.mid-pdf', $data);
            $filename = 'Rapor-Mid-'.$siswa->nama_siswa.'.pdf';
        } else {
            $data = $this->rapor->getDataRaporSemester($siswaId, $taId, $semesterId);
            $pdf = Pdf::loadView('tu.rapor.semester-pdf', $data);
            $filename = 'Rapor-Semester-'.$siswa->nama_siswa.'.pdf';
        }

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($filename);
    }

    private function generateZipBatch(array $siswaIds, string $jenis, int $taId, int $semesterId, Kelas $kelas)
    {
        $zip = new ZipArchive;
        $zipFileName = 'Rapor-'.ucfirst($jenis).'-'.str_replace(' ', '_', $kelas->nama_kelas).'.zip';
        $zipPath = storage_path('app/temp/'.$zipFileName);

        if (! file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($siswaIds as $siswaId) {
                $siswa = Siswa::find($siswaId);
                if (! $siswa) {
                    continue;
                }

                if ($jenis === 'mid') {
                    $data = $this->rapor->getDataRaporMid($siswaId, $taId, $semesterId);
                    $pdf = Pdf::loadView('tu.rapor.mid-pdf', $data);
                    $filename = 'Rapor-Mid-'.$siswa->nama_siswa.'.pdf';
                } else {
                    $data = $this->rapor->getDataRaporSemester($siswaId, $taId, $semesterId);
                    $pdf = Pdf::loadView('tu.rapor.semester-pdf', $data);
                    $filename = 'Rapor-Semester-'.$siswa->nama_siswa.'.pdf';
                }

                $pdf->setPaper('a4', 'portrait');
                $pdfContent = $pdf->output();
                $zip->addFromString($filename, $pdfContent);
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function isWali(int $kelasId): bool
    {
        $sekolah = Sekolah::first();

        return auth()->user()->kelasWali()
            ->where('kelas_wali.tahun_pelajaran_id', session('selected_tahun', $sekolah?->tahun_aktif))
            ->where('kelas_wali.semester_id', session('selected_semester', $sekolah?->semester_aktif))
            ->where('kelas_id', $kelasId)->exists();
    }
}
