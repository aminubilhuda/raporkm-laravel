<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\MapelKelas;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapelKelasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ]);

        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $data = MapelKelas::with(['mapel', 'user', 'kelas'])
            ->when($request->filled('kelas_id'), fn ($q) => $q->where('kelas_id', $request->integer('kelas_id')))
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('mapel', function ($m) use ($search) {
                        $m->where('nama_mapel', 'like', "%{$search}%")
                          ->orWhere('kode', 'like', "%{$search}%");
                    })->orWhereHas('user', function ($u) use ($search) {
                        $u->where('nama', 'like', "%{$search}%");
                    })->orWhereHas('kelas', function ($k) use ($search) {
                        $k->where('nama_kelas', 'like', "%{$search}%");
                    });
                });
            })
            ->with('mapel.kelompokMapel')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data->map(fn ($mk) => [
                'id' => $mk->id,
                'mapel_id' => $mk->mapel_id,
                'nama_mapel' => $mk->mapel?->nama_mapel,
                'kode' => $mk->mapel?->kode,
                'kelompok' => $mk->mapel?->kelompokMapel?->nama,
                'user_id' => $mk->user_id,
                'nama_guru' => $mk->user?->nama,
                'nama_kelas' => $mk->kelas?->nama_kelas,
                'kkm' => $mk->kkm,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mapel_id' => ['required', 'exists:mapel,id'],
            'user_id' => ['required', 'exists:users,id'],
            'kkm' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $sekolah = Sekolah::first();
        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $mk = MapelKelas::create($validated);
        $mk->load(['mapel', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Mapel kelas berhasil ditambahkan.',
            'data' => $mk,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $mk = MapelKelas::find($id);

        if (! $mk) {
            return response()->json(['success' => false, 'message' => 'Mapel kelas tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'user_id' => ['sometimes', 'exists:users,id'],
            'kkm' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ]);

        $mk->update($validated);
        $mk->refresh()->load(['mapel', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Mapel kelas berhasil diperbarui.',
            'data' => $mk,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $mk = MapelKelas::find($id);

        if (! $mk) {
            return response()->json(['success' => false, 'message' => 'Mapel kelas tidak ditemukan.'], 404);
        }

        $mk->delete();

        return response()->json(['success' => true, 'message' => 'Mapel kelas berhasil dihapus.']);
    }

    public function batch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.kelas_id' => ['required', 'exists:kelas,id'],
            'items.*.mapel_id' => ['required', 'exists:mapel,id'],
            'items.*.user_id' => ['required', 'exists:users,id'],
            'items.*.kkm' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $created = 0;
        foreach ($validated['items'] as $item) {
            MapelKelas::updateOrCreate(
                [
                    'kelas_id' => $item['kelas_id'],
                    'mapel_id' => $item['mapel_id'],
                    'tahun_pelajaran_id' => $tpId,
                    'semester_id' => $semId,
                ],
                [
                    'user_id' => $item['user_id'],
                    'kkm' => $item['kkm'] ?? 75,
                ],
            );
            $created++;
        }

        return response()->json([
            'success' => true,
            'message' => "$created mapel kelas berhasil disimpan.",
        ]);
    }
}
