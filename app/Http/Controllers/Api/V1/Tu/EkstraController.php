<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\PembinaEskul;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EkstraController extends Controller
{
    public function index(): JsonResponse
    {
        $eskul = Eskul::withCount('siswaEskul as jumlah_siswa')
            ->with('pembinaEskul.user')
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $eskul->map(fn ($e) => [
                'id' => $e->id,
                'nama_eskul' => $e->nama_eskul,
                'keterangan' => $e->keterangan,
                'jumlah_siswa' => $e->jumlah_siswa,
                'pembina' => $e->pembinaEskul->map(fn ($p) => [
                    'id' => $p->id,
                    'user_id' => $p->user_id,
                    'nama' => $p->user?->nama,
                ]),
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_eskul' => ['required', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $eskul = Eskul::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ekstrakurikuler berhasil dibuat.',
            'data' => $eskul,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $eskul = Eskul::find($id);

        if (! $eskul) {
            return response()->json(['success' => false, 'message' => 'Ekstrakurikuler tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'nama_eskul' => ['sometimes', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $eskul->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ekstrakurikuler berhasil diperbarui.',
            'data' => $eskul,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $eskul = Eskul::find($id);

        if (! $eskul) {
            return response()->json(['success' => false, 'message' => 'Ekstrakurikuler tidak ditemukan.'], 404);
        }

        $eskul->delete();

        return response()->json(['success' => true, 'message' => 'Ekstrakurikuler berhasil dihapus.']);
    }

    public function storePembina(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'eskul_id' => ['required', 'exists:eskul,id'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $sekolah = Sekolah::first();

        $pembina = PembinaEskul::updateOrCreate(
            [
                'eskul_id' => $validated['eskul_id'],
                'user_id' => $validated['user_id'],
                'tahun_pelajaran_id' => $sekolah?->tahun_aktif,
            ],
            [],
        );

        return response()->json([
            'success' => true,
            'message' => 'Pembina ekstrakurikuler berhasil ditambahkan.',
            'data' => $pembina,
        ], 201);
    }

    public function destroyPembina(string $id): JsonResponse
    {
        $pembina = PembinaEskul::find($id);

        if (! $pembina) {
            return response()->json(['success' => false, 'message' => 'Pembina tidak ditemukan.'], 404);
        }

        $pembina->delete();

        return response()->json(['success' => true, 'message' => 'Pembina ekstrakurikuler berhasil dihapus.']);
    }
}
