<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\PembagianRaport;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index(): JsonResponse
    {
        $sekolah = Sekolah::first();
        $tp = TahunPelajaran::find($sekolah?->tahun_aktif);
        $sem = Semester::find($sekolah?->semester_aktif);
        $pembagian = PembagianRaport::where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
            ->where('semester_id', $sekolah?->semester_aktif)
            ->first();

        $tpList = TahunPelajaran::orderBy('id', 'desc')->get();
        $semList = Semester::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sekolah' => [
                    'id' => $sekolah?->id,
                    'nama_sekolah' => $sekolah?->nama_sekolah,
                    'npsn' => $sekolah?->npsn,
                    'alamat' => $sekolah?->alamat,
                    'email' => $sekolah?->email,
                    'kontak' => $sekolah?->kontak,
                ],
                'tahun_pelajaran_aktif' => $tp ? [
                    'id' => $tp->id,
                    'tahun' => $tp->tahun,
                    'status' => $tp->status,
                ] : null,
                'semester_aktif' => $sem ? [
                    'id' => $sem->id,
                    'nama' => $sem->nama,
                    'status' => $sem->status,
                ] : null,
                'pembagian_raport' => $pembagian ? [
                    'id' => $pembagian->id,
                    'tanggal_mid' => $pembagian->tanggal_mid,
                    'tanggal_semester' => $pembagian->tanggal_semester,
                ] : null,
                'tahun_pelajaran_list' => $tpList->map(fn ($t) => [
                    'id' => $t->id,
                    'tahun' => $t->tahun,
                    'status' => (int) $t->status,
                ]),
                'semester_list' => $semList->map(fn ($s) => [
                    'id' => $s->id,
                    'nama' => $s->nama,
                    'urutan' => $s->urutan,
                    'status' => (int) $s->status,
                ]),
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tahun_aktif' => ['nullable', 'exists:tahun_pelajaran,id'],
            'tahun_pelajaran_aktif_id' => ['nullable', 'exists:tahun_pelajaran,id'],
            'semester_aktif' => ['nullable', 'exists:semester,id'],
            'semester_aktif_id' => ['nullable', 'exists:semester,id'],
            'tanggal_mid' => ['nullable', 'date'],
            'tanggal_semester' => ['nullable', 'date'],
        ]);

        $sekolah = Sekolah::first();

        if (! $sekolah) {
            return response()->json(['success' => false, 'message' => 'Data sekolah tidak ditemukan.'], 404);
        }

        $tahunId = $validated['tahun_pelajaran_aktif_id'] ?? $validated['tahun_aktif'] ?? null;
        if ($tahunId) {
            $sekolah->update(['tahun_aktif' => $tahunId]);
        }

        $semesterId = $validated['semester_aktif_id'] ?? $validated['semester_aktif'] ?? null;
        if ($semesterId) {
            $sekolah->update(['semester_aktif' => $semesterId]);
        }

        if (isset($validated['tanggal_mid']) || isset($validated['tanggal_semester'])) {
            PembagianRaport::updateOrCreate(
                [
                    'tahun_pelajaran_id' => $sekolah->tahun_aktif,
                    'semester_id' => $sekolah->semester_aktif,
                ],
                collect($validated)->only(['tanggal_mid', 'tanggal_semester'])->toArray(),
            );
        }

        return $this->index();
    }

    public function tahunPelajaran(): JsonResponse
    {
        $tpList = TahunPelajaran::withCount('kelas')->latest('id')->get();

        return response()->json([
            'success' => true,
            'data' => $tpList->map(fn ($tp) => [
                'id' => $tp->id,
                'tahun' => $tp->tahun,
                'status' => (bool) $tp->status,
                'jumlah_kelas' => $tp->kelas_count,
            ]),
        ]);
    }

    public function semester(): JsonResponse
    {
        $semList = Semester::latest('id')->get();

        return response()->json([
            'success' => true,
            'data' => $semList->map(fn ($s) => [
                'id' => $s->id,
                'nama' => $s->nama,
                'urutan' => $s->urutan,
                'status' => (bool) $s->status,
            ]),
        ]);
    }
}
