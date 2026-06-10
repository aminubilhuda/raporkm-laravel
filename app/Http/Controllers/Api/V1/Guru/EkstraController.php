<?php

namespace App\Http\Controllers\Api\V1\Guru;

use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\Sekolah;
use App\Models\SiswaEskul;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EkstraController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;

        $eskul = Eskul::whereHas('pembinaEskul', function ($q) use ($user, $tpId) {
            $q->where('user_id', $user->id)
                ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId));
        })->withCount('siswaEskul as jumlah_siswa')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $eskul->map(fn ($e) => [
                'id' => $e->id,
                'nama_eskul' => $e->nama_eskul,
                'keterangan' => $e->keterangan,
                'jumlah_siswa' => $e->jumlah_siswa,
            ]),
        ]);
    }

    public function siswa(Request $request, string $eskulId): JsonResponse
    {
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;

        $siswaEskul = SiswaEskul::with(['siswa', 'eskul'])
            ->where('eskul_id', $eskulId)
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->get();

        return response()->json([
            'success' => true,
            'data' => $siswaEskul->map(fn ($se) => [
                'id' => $se->id,
                'siswa_id' => $se->siswa_id,
                'nama_siswa' => $se->siswa?->nama_siswa,
                'predikat' => $se->predikat,
                'keterangan' => $se->keterangan,
            ]),
        ]);
    }

    public function storePenilaian(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();

        $validated = $request->validate([
            'siswa_eskul_id' => ['required', 'exists:siswa_eskul,id'],
            'predikat' => ['required', 'string', 'max:30'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $siswaEskul = SiswaEskul::find($validated['siswa_eskul_id']);
        $siswaEskul->update([
            'predikat' => $validated['predikat'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Penilaian ekstrakurikuler berhasil disimpan.',
            'data' => $siswaEskul,
        ]);
    }
}
