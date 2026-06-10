<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\SiswaKelas;
use App\Models\SiswaPrakerin;
use App\Services\RaporService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ZipArchive;

class CetakRaporController extends Controller
{
    public function __construct(
        private RaporService $rapor,
    ) {}

    public function index(): JsonResponse
    {
        $siswaList = SiswaKelas::with(['siswa', 'kelas.tingkat', 'kelas.kompetensiKeahlian'])
            ->where('status', 'aktif')
            ->get()
            ->map(fn ($sk) => [
                'siswa_id' => $sk->siswa?->id,
                'nama_siswa' => $sk->siswa?->nama_siswa,
                'nisn' => $sk->siswa?->nisn,
                'kelas_id' => $sk->kelas_id,
                'nama_kelas' => $sk->kelas?->nama_kelas,
                'tingkat' => $sk->kelas?->tingkat?->nama,
            ]);

        return response()->json([
            'success' => true,
            'data' => $siswaList,
        ]);
    }

    public function cetak(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'siswa_id' => ['required', 'array', 'min:1'],
            'siswa_id.*' => ['exists:siswa,id'],
            'jenis' => ['required', 'in:semester,mid,pkl'],
            'tahun_pelajaran_id' => ['nullable', 'exists:tahun_pelajaran,id'],
            'semester_id' => ['nullable', 'exists:semester,id'],
        ]);

        if ($validated['jenis'] === 'pkl') {
            return $this->generatePklPdf($validated['siswa_id'], $validated['tahun_pelajaran_id'], $validated['semester_id']);
        }

        if (count($validated['siswa_id']) === 1) {
            return $this->generateSinglePdf($validated['siswa_id'][0], $validated['jenis'], $validated['tahun_pelajaran_id'], $validated['semester_id']);
        }

        return $this->generateZipBatch($validated['siswa_id'], $validated['jenis'], $validated['tahun_pelajaran_id'], $validated['semester_id']);
    }

    private function generateSinglePdf(int $siswaId, string $jenis, ?int $taId, ?int $semId): JsonResponse
    {
        try {
            $data = $jenis === 'mid'
                ? $this->rapor->getDataRaporMid($siswaId, $taId, $semId)
                : $this->rapor->getDataRaporSemester($siswaId, $taId, $semId);

            $view = $jenis === 'mid' ? 'tu.rapor.mid-pdf' : 'tu.rapor.semester-pdf';
            $pdf = Pdf::loadView($view, $data)->setPaper('a4', 'portrait');
            $filename = 'rapor_'.$jenis.'_'.$data['siswa']->nisn.'_'.$data['kelas']->nama_kelas.'.pdf';

            return response()->json([
                'success' => true,
                'message' => 'PDF rapor berhasil dibuat.',
                'data' => [
                    'filename' => $filename,
                    'pdf_base64' => base64_encode($pdf->output()),
                    'jenis' => $jenis,
                    'siswa' => [
                        'id' => $data['siswa']->id,
                        'nama_siswa' => $data['siswa']->nama_siswa,
                        'nisn' => $data['siswa']->nisn,
                    ],
                    'kelas' => $data['kelas']->nama_kelas,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF: '.$e->getMessage(),
            ], 500);
        }
    }

    private function generateZipBatch(array $siswaIds, string $jenis, ?int $taId, ?int $semId): JsonResponse
    {
        try {
            $tempDir = storage_path('app/temp');
            if (! is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $zipPath = $tempDir.'/rapor_batch_'.time().'.zip';
            $zip = new ZipArchive;
            $success = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            if ($success !== true) {
                return response()->json(['success' => false, 'message' => 'Gagal membuat ZIP file.'], 500);
            }

            $generated = 0;
            foreach ($siswaIds as $siswaId) {
                try {
                    $data = $jenis === 'mid'
                        ? $this->rapor->getDataRaporMid($siswaId, $taId, $semId)
                        : $this->rapor->getDataRaporSemester($siswaId, $taId, $semId);

                    $view = $jenis === 'mid' ? 'tu.rapor.mid-pdf' : 'tu.rapor.semester-pdf';
                    $pdf = Pdf::loadView($view, $data)->setPaper('a4', 'portrait');
                    $filename = 'rapor_'.$jenis.'_'.$data['siswa']->nisn.'.pdf';
                    $zip->addFromString($filename, $pdf->output());
                    $generated++;
                } catch (\Exception $e) {
                    continue;
                }
            }

            $zip->close();
            $zipContent = file_get_contents($zipPath);
            @unlink($zipPath);

            return response()->json([
                'success' => true,
                'message' => "PDF rapor batch berhasil dibuat ($generated file).",
                'data' => [
                    'filename' => 'rapor_batch_'.time().'.zip',
                    'zip_base64' => base64_encode($zipContent),
                    'jenis' => $jenis,
                    'total_generated' => $generated,
                    'total_requested' => count($siswaIds),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat ZIP: '.$e->getMessage(),
            ], 500);
        }
    }

    private function generatePklPdf(array $siswaIds, ?int $taId, ?int $semId): JsonResponse
    {
        try {
            $siswaPrakerin = SiswaPrakerin::whereIn('siswa_id', $siswaIds)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semId, fn ($q) => $q->where('semester_id', $semId))
                ->pluck('id');

            if ($siswaPrakerin->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Data prakerin tidak ditemukan.'], 404);
            }

            if ($siswaPrakerin->count() === 1) {
                $data = $this->rapor->getDataRaporPkl($siswaPrakerin->first());
                $pdf = Pdf::loadView('tu.rapor.pkl-pdf', $data)->setPaper('a4', 'portrait');
                $filename = 'rapor_pkl_'.$data['siswa']->nisn.'.pdf';

                return response()->json([
                    'success' => true,
                    'message' => 'PDF rapor PKL berhasil dibuat.',
                    'data' => [
                        'filename' => $filename,
                        'pdf_base64' => base64_encode($pdf->output()),
                        'jenis' => 'pkl',
                        'siswa' => [
                            'id' => $data['siswa']->id,
                            'nama_siswa' => $data['siswa']->nama_siswa,
                            'nisn' => $data['siswa']->nisn,
                        ],
                        'kelas' => $data['kelas']->nama_kelas,
                    ],
                ]);
            }

            $tempDir = storage_path('app/temp');
            if (! is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $zipPath = $tempDir.'/rapor_pkl_batch_'.time().'.zip';
            $zip = new ZipArchive;
            $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $generated = 0;
            foreach ($siswaPrakerin as $spId) {
                try {
                    $data = $this->rapor->getDataRaporPkl($spId);
                    $pdf = Pdf::loadView('tu.rapor.pkl-pdf', $data)->setPaper('a4', 'portrait');
                    $filename = 'rapor_pkl_'.$data['siswa']->nisn.'.pdf';
                    $zip->addFromString($filename, $pdf->output());
                    $generated++;
                } catch (\Exception $e) {
                    continue;
                }
            }

            $zip->close();
            $zipContent = file_get_contents($zipPath);
            @unlink($zipPath);

            return response()->json([
                'success' => true,
                'message' => "PDF rapor PKL batch berhasil dibuat ($generated file).",
                'data' => [
                    'filename' => 'rapor_pkl_batch_'.time().'.zip',
                    'zip_base64' => base64_encode($zipContent),
                    'jenis' => 'pkl',
                    'total_generated' => $generated,
                    'total_requested' => count($siswaIds),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF PKL: '.$e->getMessage(),
            ], 500);
        }
    }
}
