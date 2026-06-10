<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\KelasWali;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KelasWaliController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();
        $tpId = $request->integer('tahun_pelajaran_id') ?? $sekolah?->tahun_aktif;
        $semId = $request->integer('semester_id') ?? $sekolah?->semester_aktif;

        $data = KelasWali::with(['kelas.tingkat', 'kelas.kompetensiKeahlian', 'user'])
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data->map(fn ($kw) => [
                'id' => $kw->id,
                'kelas_id' => $kw->kelas_id,
                'nama_kelas' => $kw->kelas?->nama_kelas,
                'tingkat' => $kw->kelas?->tingkat?->nama,
                'user_id' => $kw->user_id,
                'nama_guru' => $kw->user?->nama,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $sekolah = Sekolah::first();
        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $kw = KelasWali::create($validated);
        $kw->load(['kelas', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Wali kelas berhasil ditambahkan.',
            'data' => $kw,
        ], 201);
    }

    public function destroy(string $id): JsonResponse
    {
        $kw = KelasWali::find($id);

        if (! $kw) {
            return response()->json(['success' => false, 'message' => 'Wali kelas tidak ditemukan.'], 404);
        }

        $kw->delete();

        return response()->json(['success' => true, 'message' => 'Wali kelas berhasil dihapus.']);
    }
}
