<?php

namespace App\Http\Controllers\Api\V1\Guru;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TujuanPembelajaranController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mapel_id' => ['required', 'exists:mapel,id'],
        ]);

        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $tp = TujuanPembelajaran::where('kelas_id', $request->integer('kelas_id'))
            ->where('mapel_id', $request->integer('mapel_id'))
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->orderBy('kode_tp')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tp->map(fn ($t) => [
                'id' => $t->id,
                'kode_tp' => $t->kode_tp,
                'nama_tp' => $t->nama_tp,
                'mapel_id' => $t->mapel_id,
                'kelas_id' => $t->kelas_id,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mapel_id' => ['required', 'exists:mapel,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'kode_tp' => ['required', 'string', 'max:50'],
            'nama_tp' => ['required', 'string'],
        ]);

        $sekolah = Sekolah::first();
        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $tp = TujuanPembelajaran::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tujuan pembelajaran berhasil dibuat.',
            'data' => $tp,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $tp = TujuanPembelajaran::find($id);

        if (! $tp) {
            return response()->json(['success' => false, 'message' => 'Tujuan pembelajaran tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'kode_tp' => ['sometimes', 'string', 'max:50'],
            'nama_tp' => ['sometimes', 'string'],
        ]);

        $tp->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tujuan pembelajaran berhasil diperbarui.',
            'data' => $tp,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $tp = TujuanPembelajaran::find($id);

        if (! $tp) {
            return response()->json(['success' => false, 'message' => 'Tujuan pembelajaran tidak ditemukan.'], 404);
        }

        $tp->delete();

        return response()->json(['success' => true, 'message' => 'Tujuan pembelajaran berhasil dihapus.']);
    }
}
