<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MapelResource;
use App\Models\Mapel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Mapel::with('kelompokMapel');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama_mapel', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelompok_mapel_id')) {
            $query->where('kelompok_mapel_id', $request->integer('kelompok_mapel_id'));
        }

        $perPage = $request->input('per_page', 15);

        if ($perPage === 'all') {
            $mapel = $query
                ->orderByRaw('CASE WHEN urutan IS NULL OR urutan = 0 THEN 1 ELSE 0 END')
                ->orderBy('urutan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => MapelResource::collection($mapel),
            ]);
        }

        $paginator = $query
            ->orderByRaw('CASE WHEN urutan IS NULL OR urutan = 0 THEN 1 ELSE 0 END')
            ->orderBy('urutan')
            ->paginate((int) $perPage)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'data' => MapelResource::collection($paginator->getCollection()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => (int) $perPage,
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $mapel = Mapel::with('kelompokMapel')->find($id);

        if (! $mapel) {
            return response()->json([
                'success' => false,
                'message' => 'Mapel tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new MapelResource($mapel),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kelompok_mapel_id' => ['required', 'exists:kelompok_mapel,id'],
            'kode' => ['nullable', 'string', 'max:20', 'unique:mapel,kode'],
            'nama_mapel' => ['required', 'string', 'max:200'],
            'kkm' => ['required', 'integer', 'min:0', 'max:100'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'kurikulum_id' => ['nullable', 'exists:ref_kurikulum,id'],
        ]);

        $mapel = Mapel::create($validated);
        $mapel->load('kelompokMapel');

        return response()->json([
            'success' => true,
            'message' => 'Mapel berhasil dibuat.',
            'data' => new MapelResource($mapel),
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $mapel = Mapel::find($id);

        if (! $mapel) {
            return response()->json([
                'success' => false,
                'message' => 'Mapel tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'kelompok_mapel_id' => ['sometimes', 'exists:kelompok_mapel,id'],
            'kode' => ['sometimes', 'string', 'max:20', 'unique:mapel,kode,'.$id],
            'nama_mapel' => ['sometimes', 'string', 'max:200'],
            'kkm' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'kurikulum_id' => ['nullable', 'exists:ref_kurikulum,id'],
        ]);

        $mapel->update($validated);
        $mapel->refresh()->load('kelompokMapel');

        return response()->json([
            'success' => true,
            'message' => 'Mapel berhasil diperbarui.',
            'data' => new MapelResource($mapel),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $mapel = Mapel::find($id);

        if (! $mapel) {
            return response()->json([
                'success' => false,
                'message' => 'Mapel tidak ditemukan.',
            ], 404);
        }

        $mapel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mapel berhasil dihapus.',
        ]);
    }
}
