<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Tu\StoreSiswaRequest;
use App\Http\Requests\Api\V1\Tu\UpdateSiswaRequest;
use App\Http\Resources\V1\SiswaResource;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Siswa::with(['siswaKelas.kelas.tingkat', 'siswaKelas.kelas.kompetensiKeahlian'])
            ->where('aktif', 1);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nama_siswa', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswaKelas', function ($q) use ($request) {
                $q->where('kelas_id', $request->integer('kelas_id'))
                    ->where('status', 'aktif');
            });
        }

        if ($request->filled('kelamin')) {
            $query->where('kelamin', $request->integer('kelamin'));
        }

        if ($request->filled('agama')) {
            $query->where('agama', $request->integer('agama'));
        }

        $perPage = $request->input('per_page', 15);

        if ($perPage === 'all') {
            $siswa = $query->latest()->get();

            return response()->json([
                'success' => true,
                'data' => SiswaResource::collection($siswa),
            ]);
        }

        $paginator = $query->latest()->paginate((int) $perPage)->withQueryString();

        return response()->json([
            'success' => true,
            'data' => SiswaResource::collection($paginator->getCollection()),
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
        $siswa = Siswa::with(['siswaKelas.kelas.tingkat', 'siswaKelas.kelas.kompetensiKeahlian', 'kompetensiKeahlian'])
            ->find($id);

        if (! $siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new SiswaResource($siswa),
        ]);
    }

    public function store(StoreSiswaRequest $request): JsonResponse
    {
        $siswa = Siswa::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil dibuat.',
            'data' => new SiswaResource($siswa),
        ], 201);
    }

    public function update(UpdateSiswaRequest $request, string $id): JsonResponse
    {
        $siswa = Siswa::find($id);

        if (! $siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan.',
            ], 404);
        }

        $siswa->update($request->validated());

        $siswa->refresh()->load(['siswaKelas.kelas.tingkat', 'siswaKelas.kelas.kompetensiKeahlian']);

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil diperbarui.',
            'data' => new SiswaResource($siswa),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $siswa = Siswa::find($id);

        if (! $siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan.',
            ], 404);
        }

        $siswa->update(['aktif' => 0]);
        $siswa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil dihapus.',
        ]);
    }
}
