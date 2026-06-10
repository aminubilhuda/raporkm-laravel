<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\KelasResource;
use App\Models\Kelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Kelas::with(['tingkat', 'kompetensiKeahlian', 'tahunPelajaran', 'semester'])
            ->withCount('siswaKelas');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama_kelas', 'like', "%{$search}%");
        }

        if ($request->filled('tingkat_id')) {
            $query->where('tingkat_id', $request->integer('tingkat_id'));
        }

        if ($request->filled('kompetensi_keahlian_id')) {
            $query->where('kompetensi_keahlian_id', $request->integer('kompetensi_keahlian_id'));
        }

        if ($request->filled('tahun_pelajaran_id')) {
            $query->where('tahun_pelajaran_id', $request->integer('tahun_pelajaran_id'));
        }

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->integer('semester_id'));
        }

        $perPage = $request->input('per_page', 15);

        if ($perPage === 'all') {
            $kelas = $query->orderBy('nama_kelas')->get();

            return response()->json([
                'success' => true,
                'data' => KelasResource::collection($kelas),
            ]);
        }

        $paginator = $query->orderBy('nama_kelas')->paginate((int) $perPage)->withQueryString();

        return response()->json([
            'success' => true,
            'data' => KelasResource::collection($paginator->getCollection()),
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
        $kelas = Kelas::with(['tingkat', 'kompetensiKeahlian', 'tahunPelajaran', 'semester'])
            ->withCount('siswaKelas')
            ->find($id);

        if (! $kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new KelasResource($kelas),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:50'],
            'tingkat_id' => ['required', 'exists:tingkat,id'],
            'kompetensi_keahlian_id' => ['required', 'exists:kompetensi_keahlian,id'],
            'tahun_pelajaran_id' => ['required', 'exists:tahun_pelajaran,id'],
            'semester_id' => ['required', 'exists:semester,id'],
        ]);

        $kelas = Kelas::create($validated);
        $kelas->load(['tingkat', 'kompetensiKeahlian', 'tahunPelajaran', 'semester']);

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dibuat.',
            'data' => new KelasResource($kelas),
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'nama_kelas' => ['sometimes', 'string', 'max:50'],
            'tingkat_id' => ['sometimes', 'exists:tingkat,id'],
            'kompetensi_keahlian_id' => ['sometimes', 'exists:kompetensi_keahlian,id'],
            'tahun_pelajaran_id' => ['sometimes', 'exists:tahun_pelajaran,id'],
            'semester_id' => ['sometimes', 'exists:semester,id'],
        ]);

        $kelas->update($validated);
        $kelas->refresh()->load(['tingkat', 'kompetensiKeahlian', 'tahunPelajaran', 'semester']);

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diperbarui.',
            'data' => new KelasResource($kelas),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan.',
            ], 404);
        }

        $kelas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus.',
        ]);
    }
}
