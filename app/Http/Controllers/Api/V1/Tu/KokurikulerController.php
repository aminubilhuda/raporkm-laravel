<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\DeskripsiKokurikuler;
use App\Models\DimensiKokurikuler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KokurikulerController extends Controller
{
    public function index(): JsonResponse
    {
        $dimensi = DimensiKokurikuler::with('deskripsiKokurikuler')
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $dimensi->map(fn ($d) => [
                'id' => $d->id,
                'nama' => $d->nama,
                'keterangan' => $d->keterangan,
                'deskripsi' => $d->deskripsiKokurikuler->map(fn ($dk) => [
                    'id' => $dk->id,
                    'predikat' => $dk->predikat,
                    'deskripsi' => $dk->deskripsi,
                ]),
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $dimensi = DimensiKokurikuler::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dimensi kokurikuler berhasil dibuat.',
            'data' => $dimensi,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $dimensi = DimensiKokurikuler::find($id);

        if (! $dimensi) {
            return response()->json(['success' => false, 'message' => 'Dimensi tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'nama' => ['sometimes', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $dimensi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dimensi kokurikuler berhasil diperbarui.',
            'data' => $dimensi,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $dimensi = DimensiKokurikuler::find($id);

        if (! $dimensi) {
            return response()->json(['success' => false, 'message' => 'Dimensi tidak ditemukan.'], 404);
        }

        $dimensi->delete();

        return response()->json(['success' => true, 'message' => 'Dimensi kokurikuler berhasil dihapus.']);
    }

    public function storeDeskripsi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dimensi_kokurikuler_id' => ['required', 'exists:dimensi_kokurikuler,id'],
            'predikat' => ['required', 'string', 'max:20'],
            'deskripsi' => ['required', 'string'],
        ]);

        $desk = DeskripsiKokurikuler::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Deskripsi kokurikuler berhasil dibuat.',
            'data' => $desk,
        ], 201);
    }

    public function destroyDeskripsi(string $id): JsonResponse
    {
        $desk = DeskripsiKokurikuler::find($id);

        if (! $desk) {
            return response()->json(['success' => false, 'message' => 'Deskripsi tidak ditemukan.'], 404);
        }

        $desk->delete();

        return response()->json(['success' => true, 'message' => 'Deskripsi kokurikuler berhasil dihapus.']);
    }
}
