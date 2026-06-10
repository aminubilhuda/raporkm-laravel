<?php

namespace App\Http\Controllers\Api\V1\Guru;

use App\Http\Controllers\Controller;
use App\Models\CatatanWali;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatatanRaporController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
        ]);

        $catatan = CatatanWali::with('siswa')
            ->where('kelas_id', $request->integer('kelas_id'))
            ->where('user_id', $user->id)
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'siswa_id' => $c->siswa_id,
                'nama_siswa' => $c->siswa?->nama_siswa,
                'catatan' => $c->catatan,
            ]);

        return response()->json([
            'success' => true,
            'data' => $catatan,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $sekolah = Sekolah::first();

        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswa,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'catatan' => ['required', 'string'],
        ]);

        $catatan = CatatanWali::updateOrCreate(
            [
                'siswa_id' => $validated['siswa_id'],
                'kelas_id' => $validated['kelas_id'],
                'user_id' => $user->id,
                'tahun_pelajaran_id' => $sekolah?->tahun_aktif,
                'semester_id' => $sekolah?->semester_aktif,
            ],
            ['catatan' => $validated['catatan']],
        );

        return response()->json([
            'success' => true,
            'message' => 'Catatan rapor berhasil disimpan.',
            'data' => $catatan,
        ], 201);
    }
}
