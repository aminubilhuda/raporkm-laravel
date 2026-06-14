<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\NilaiFormatif;
use App\Models\NilaiSumatifAs;
use App\Models\NilaiSumatifPh;
use App\Models\NilaiSumatifTs;
use App\Models\Sekolah;
use App\Models\SiswaKelas;
use App\Models\TujuanPembelajaran;
use App\Services\PenilaianService;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function __construct(private PenilaianService $penilaianService)
    {
    }

    public function index(?Kelas $kelas = null, ?Mapel $mapel = null)
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        $mapelKelasList = $user->mapelKelas()
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->with('mapel', 'kelas.tingkat', 'kelas.kompetensiKeahlian')
            ->get()
            ->sortBy(fn ($mk) => $mk->mapel?->urutan ?? 0);

        $authorized = $kelas && $mapel && $mapelKelasList->contains(fn ($mk) => $mk->kelas_id === $kelas->id && $mk->mapel_id === $mapel->id);

        $siswa = collect();
        $tujuanPembelajaran = collect();
        $nilaiFormatif = collect();
        $nilaiSumatifPh = collect();
        $nilaiSumatifTs = collect();
        $nilaiSumatifAs = collect();

        if ($authorized) {
            [$siswa, $tujuanPembelajaran, $nilaiFormatif, $nilaiSumatifPh, $nilaiSumatifTs, $nilaiSumatifAs] = $this->loadPenilaianData($kelas, $mapel, $taId, $semesterId);
        }

        return view('guru.penilaian.index', compact(
            'mapelKelasList', 'kelas', 'mapel', 'authorized',
            'siswa', 'tujuanPembelajaran', 'nilaiFormatif', 'nilaiSumatifPh',
            'nilaiSumatifTs', 'nilaiSumatifAs'
        ));
    }

    public function storeFormatif(Request $request)
    {
        return $this->handleBatchStore($request, NilaiFormatif::class, ['nilai'], tpKeyed: true);
    }

    public function storeSumatifPh(Request $request)
    {
        return $this->handleBatchStore($request, NilaiSumatifPh::class, ['nilai', 'deskripsi'], tpKeyed: true);
    }

    public function storeSumatifTs(Request $request)
    {
        return $this->handleBatchStore($request, NilaiSumatifTs::class, ['nilai', 'deskripsi']);
    }

    public function storeSumatifAs(Request $request)
    {
        return $this->handleBatchStore($request, NilaiSumatifAs::class, ['nilai', 'deskripsi']);
    }

    /**
     * Load all penilaian data for a class/mapel combination.
     *
     * @return array<\Illuminate\Support\Collection>
     */
    private function loadPenilaianData(Kelas $kelas, Mapel $mapel, ?int $taId, ?int $semesterId): array
    {
        $siswa = SiswaKelas::where('kelas_id', $kelas->id)
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->with('siswa')
            ->get();

        $tujuanPembelajaran = TujuanPembelajaran::where('mapel_id', $mapel->id)
            ->where('kelas_id', $kelas->id)
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->get();

        $siswaIds = $siswa->pluck('siswa_id');
        $tpIds = $tujuanPembelajaran->pluck('id');

        $nilaiFormatif = NilaiFormatif::whereIn('siswa_id', $siswaIds)
            ->whereIn('tujuan_pembelajaran_id', $tpIds)
            ->where('mapel_id', $mapel->id)
            ->where('kelas_id', $kelas->id)
            ->get()
            ->keyBy(fn ($n) => "{$n->siswa_id}_{$n->tujuan_pembelajaran_id}");

        $nilaiSumatifPh = NilaiSumatifPh::whereIn('siswa_id', $siswaIds)
            ->whereIn('tujuan_pembelajaran_id', $tpIds)
            ->where('mapel_id', $mapel->id)
            ->where('kelas_id', $kelas->id)
            ->get()
            ->keyBy(fn ($n) => "{$n->siswa_id}_{$n->tujuan_pembelajaran_id}");

        $nilaiSumatifTs = NilaiSumatifTs::whereIn('siswa_id', $siswaIds)
            ->where('mapel_id', $mapel->id)
            ->where('kelas_id', $kelas->id)
            ->get()
            ->keyBy(fn ($n) => "{$n->siswa_id}");

        $nilaiSumatifAs = NilaiSumatifAs::whereIn('siswa_id', $siswaIds)
            ->where('mapel_id', $mapel->id)
            ->where('kelas_id', $kelas->id)
            ->get()
            ->keyBy(fn ($n) => "{$n->siswa_id}");

        return [$siswa, $tujuanPembelajaran, $nilaiFormatif, $nilaiSumatifPh, $nilaiSumatifTs, $nilaiSumatifAs];
    }

    private function handleBatchStore(Request $request, string $model, array $columns, bool $tpKeyed = false)
    {
        $saved = $this->penilaianService->batchStore($request, $model, $columns, $tpKeyed);

        return redirect()->route('guru.penilaian.index', [
            'kelas' => $request->input('kelas_id'),
            'mapel' => $request->input('mapel_id'),
        ])->with('status', "{$saved} nilai berhasil disimpan.")
          ->with('scroll_to', $request->input('_section', ''));
    }
}
