<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\NilaiFormatif;
use App\Models\NilaiMapel;
use App\Models\NilaiSumatifAs;
use App\Models\NilaiSumatifPh;
use App\Models\Sekolah;
use App\Models\SiswaKelas;
use App\Models\TujuanPembelajaran;
use App\Services\RaporService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LagerNilaiKelasController extends Controller
{
    public function __construct(private RaporService $rapor) {}

    public function index(?Kelas $kelas = null)
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = $sekolah?->tahun_aktif;
        $semesterId = $sekolah?->semester_aktif;

        $kelasList = $user->mapelKelas()
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->with('kelas.tingkat', 'kelas.kompetensiKeahlian')
            ->get()
            ->pluck('kelas')
            ->unique('id');

        $authorized = $kelas && $kelasList->firstWhere('id', $kelas->id);

        $mapelGuru = collect();
        $siswa = collect();
        $nilaiMapel = collect();
        $rataFormatif = collect();
        $rataPh = collect();
        $nilaiAs = collect();

        if ($authorized) {
            $mapelGuru = $user->mapelKelas()
                ->where('kelas_id', $kelas->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->with('mapel')
                ->get();

            $mapelIds = $mapelGuru->pluck('mapel_id');

            $siswa = SiswaKelas::where('kelas_id', $kelas->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->with('siswa')
                ->get();

            $siswaIds = $siswa->pluck('siswa_id');

            $tpIds = TujuanPembelajaran::whereIn('mapel_id', $mapelIds)
                ->where('kelas_id', $kelas->id)
                ->pluck('id');

            $rataFormatif = NilaiFormatif::whereIn('siswa_id', $siswaIds)
                ->whereIn('tujuan_pembelajaran_id', $tpIds)
                ->selectRaw('siswa_id, tujuan_pembelajaran_id, AVG(nilai) as rata')
                ->groupBy('siswa_id', 'tujuan_pembelajaran_id')
                ->get()
                ->groupBy('siswa_id')
                ->map(fn ($items) => round($items->avg('rata'), 2));

            $rataPh = NilaiSumatifPh::whereIn('siswa_id', $siswaIds)
                ->whereIn('tujuan_pembelajaran_id', $tpIds)
                ->selectRaw('siswa_id, AVG(nilai) as rata')
                ->groupBy('siswa_id')
                ->get()
                ->keyBy('siswa_id')
                ->map(fn ($item) => round($item->rata, 2));

            $nilaiAs = NilaiSumatifAs::whereIn('siswa_id', $siswaIds)
                ->whereIn('mapel_id', $mapelIds)
                ->get()
                ->keyBy(fn ($n) => "{$n->siswa_id}_{$n->mapel_id}");

            $nilaiMapel = NilaiMapel::whereIn('siswa_id', $siswaIds)
                ->whereIn('mapel_id', $mapelIds)
                ->get()
                ->keyBy(fn ($n) => "{$n->siswa_id}_{$n->mapel_id}");
        }

        return view('guru.lager-nilai-kelas.index', compact(
            'kelasList', 'kelas', 'authorized',
            'mapelGuru', 'siswa', 'nilaiMapel',
            'rataFormatif', 'rataPh', 'nilaiAs'
        ));
    }

    public function exportPdf(Request $request, Kelas $kelas, Mapel $mapel)
    {
        $sekolah = Sekolah::first();
        $taId = $sekolah?->tahun_aktif;
        $semesterId = $sekolah?->semester_aktif;

        abort_unless(
            auth()->user()->mapelKelas()
                ->where('kelas_id', $kelas->id)
                ->where('mapel_id', $mapel->id)
                ->where('tahun_pelajaran_id', $taId)
                ->where('semester_id', $semesterId)
                ->exists(),
            403
        );

        $data = $this->rapor->getDataLagerNilai($kelas->id, $mapel->id, $taId, $semesterId);
        $data['mapel'] = $mapel;
        $data['kelas'] = $kelas;
        $data['sekolah'] = $sekolah;

        $pdf = Pdf::loadView('guru.lager-nilai-kelas.pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Lager-Nilai-'.$kelas->nama_kelas.'-'.$mapel->kode.'.pdf');
    }
}
