<?php

namespace App\Http\Controllers\Api\V1\Guru;

use App\Http\Controllers\Controller;
use App\Models\NilaiPrakerin;
use App\Models\SiswaPrakerin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NilaiPrakerinController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $peserta = SiswaPrakerin::with(['siswa' => fn ($q) => $q->select('id', 'nama_siswa', 'nisn'), 'prakerin', 'kelas' => fn ($q) => $q->select('id', 'nama_kelas')])
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $peserta->map(fn ($p) => [
                'id' => $p->id,
                'siswa_id' => $p->siswa_id,
                'nama_siswa' => $p->siswa?->nama_siswa,
                'nisn' => $p->siswa?->nisn,
                'nama_kelas' => $p->kelas?->nama_kelas,
                'perusahaan' => $p->prakerin?->nama_perusahaan,
                'nilai' => $p->nilaiPrakerin->map(fn ($np) => [
                    'id' => $np->id,
                    'mapel_id' => $np->mapel_id,
                    'nilai' => $np->nilai,
                    'deskripsi' => $np->deskripsi,
                ]),
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'siswa_prakerin_id' => ['required', 'exists:siswa_prakerin,id'],
            'mapel_id' => ['required', 'exists:mapel,id'],
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $nilai = NilaiPrakerin::updateOrCreate(
            [
                'siswa_prakerin_id' => $validated['siswa_prakerin_id'],
                'mapel_id' => $validated['mapel_id'],
            ],
            ['nilai' => $validated['nilai'], 'deskripsi' => $validated['deskripsi'] ?? null],
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai prakerin berhasil disimpan.',
            'data' => $nilai,
        ], 201);
    }
}
