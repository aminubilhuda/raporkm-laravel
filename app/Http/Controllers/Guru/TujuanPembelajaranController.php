<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreTujuanPembelajaranRequest;
use App\Http\Requests\Guru\UpdateTujuanPembelajaranRequest;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Sekolah;
use App\Models\TujuanPembelajaran;

class TujuanPembelajaranController extends Controller
{
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

        $selected = null;
        if ($kelas && $mapel) {
            $selected = $mapelKelasList->firstWhere(fn ($mk) => $mk->kelas_id === $kelas->id && $mk->mapel_id === $mapel->id);
        }

        $tujuanPembelajaran = collect();
        if ($selected) {
            $tujuanPembelajaran = TujuanPembelajaran::where('mapel_id', $mapel->id)
                ->where('kelas_id', $kelas->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->withCount('nilaiFormatif')
                ->get();
        }

        return view('guru.tujuan-pembelajaran.index', compact('mapelKelasList', 'kelas', 'mapel', 'tujuanPembelajaran', 'selected'));
    }

    public function store(StoreTujuanPembelajaranRequest $request)
    {
        $user = $request->user();
        $sekolah = Sekolah::first();

        abort_unless(
            $user->mapelKelas()
                ->where('mapel_id', $request->mapel_id)
                ->where('kelas_id', $request->kelas_id)
                ->exists(),
            403
        );

        TujuanPembelajaran::create([
            'mapel_id' => $request->mapel_id,
            'kelas_id' => $request->kelas_id,
            'kode_tp' => $request->kode_tp,
            'nama_tp' => $request->nama_tp,
            'tahun_pelajaran_id' => session('selected_tahun', $sekolah?->tahun_aktif),
            'semester_id' => session('selected_semester', $sekolah?->semester_aktif),
        ]);

        return redirect()->route('guru.tujuan-pembelajaran.index', ['kelas' => $request->kelas_id, 'mapel' => $request->mapel_id])
            ->with('status', 'Tujuan pembelajaran berhasil ditambahkan.');
    }

    public function update(UpdateTujuanPembelajaranRequest $request, TujuanPembelajaran $tujuanPembelajaran)
    {
        abort_unless($this->authorized($tujuanPembelajaran), 403);

        $tujuanPembelajaran->update($request->validated());

        return redirect()->route('guru.tujuan-pembelajaran.index', [
            'kelas' => $tujuanPembelajaran->kelas_id,
            'mapel' => $tujuanPembelajaran->mapel_id,
        ])->with('status', 'Tujuan pembelajaran berhasil diperbarui.');
    }

    public function destroy(TujuanPembelajaran $tujuanPembelajaran)
    {
        abort_unless($this->authorized($tujuanPembelajaran), 403);

        $kelasId = $tujuanPembelajaran->kelas_id;
        $mapelId = $tujuanPembelajaran->mapel_id;
        $tujuanPembelajaran->delete();

        return redirect()->route('guru.tujuan-pembelajaran.index', ['kelas' => $kelasId, 'mapel' => $mapelId])
            ->with('status', 'Tujuan pembelajaran berhasil dihapus.');
    }

    private function authorized(TujuanPembelajaran $tp): bool
    {
        return auth()->user()->mapelKelas()
            ->where('mapel_id', $tp->mapel_id)
            ->where('kelas_id', $tp->kelas_id)
            ->exists();
    }
}
