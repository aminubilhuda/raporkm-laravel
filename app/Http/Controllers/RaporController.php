<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Services\RaporService;

class RaporController extends Controller
{
    public function pdf(Kelas $kelas, ?Siswa $siswa = null, bool $mid = false)
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = $sekolah?->tahun_aktif;
        $semesterId = $sekolah?->semester_aktif;

        if ($user->jabatan == 2) {
            $authorized = true;
        } elseif (in_array($user->jabatan, [3, 4])) {
            $authorized = $user->mapelKelas()
                ->where('kelas_id', $kelas->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->exists();
        } else {
            $authorized = false;
        }

        abort_unless($authorized, 403);

        $raporService = app(RaporService::class);

        if ($siswa && $siswa->exists) {
            $pdf = $raporService->generateRaporPdf($siswa->id, $taId, $semesterId, $mid);

            return $pdf->stream("rapor-{$siswa->nama_siswa}.pdf");
        }

        return view('rapor.index', compact('kelas', 'taId', 'semesterId'));
    }

    public function pdfMid(Kelas $kelas, ?Siswa $siswa = null)
    {
        return $this->pdf($kelas, $siswa, true);
    }

    public function raporPkl(int $prakerinId, Siswa $siswa)
    {
        $raporService = app(RaporService::class);
        $pdf = $raporService->generateRaporPklPdf($prakerinId, $siswa->id);

        return $pdf->stream("rapor-pkl-{$siswa->nama_siswa}.pdf");
    }
}
