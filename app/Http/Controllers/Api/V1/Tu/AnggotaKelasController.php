<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\SiswaKelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnggotaKelasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $kelasId = $request->input('kelas_id');

        $data = SiswaKelas::with(['siswa', 'kelas'])
            ->whereNull('deleted_at')
            ->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId))
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->whereHas('siswa', function ($s) use ($search) {
                    $s->where('nama_siswa', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%");
                });
            })
            ->get();

        $mapped = $data->map(fn ($sk) => [
            'id' => $sk->id,
            'siswa_id' => $sk->siswa_id,
            'nama_siswa' => $sk->siswa?->nama_siswa,
            'nisn' => $sk->siswa?->nisn,
            'nis' => $sk->siswa?->nis,
            'kelas_id' => $sk->kelas_id,
            'nama_kelas' => $sk->kelas?->nama_kelas,
            'status' => $sk->status,
            'created_at' => $sk->created_at?->toISOString(),
            'siswa' => $sk->siswa ? [
                'id' => $sk->siswa->id,
                'nama_siswa' => $sk->siswa->nama_siswa,
                'nisn' => $sk->siswa->nisn,
                'nis' => $sk->siswa->nis,
                'kelamin' => $sk->siswa->kelamin,
                'agama' => $sk->siswa->agama,
                'alamat' => $sk->siswa->alamat,
                'aktif' => (bool) $sk->siswa->aktif,
                'foto_url' => $sk->siswa->foto_url,
            ] : null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $mapped,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'siswa_id' => ['required', 'exists:siswa,id'],
            'tahun_pelajaran_id' => ['nullable', 'exists:tahun_pelajaran,id'],
            'semester_id' => ['nullable', 'exists:semester,id'],
        ]);

        $exists = SiswaKelas::where('siswa_id', $validated['siswa_id'])
            ->where('kelas_id', $validated['kelas_id'])
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah terdaftar di kelas ini.',
            ], 422);
        }

        $sekolah = Sekolah::first();
        $validated['tahun_pelajaran_id'] = $validated['tahun_pelajaran_id'] ?? $sekolah?->tahun_aktif;
        $validated['semester_id'] = $validated['semester_id'] ?? $sekolah?->semester_aktif;
        $validated['status'] = 'aktif';

        $anggota = SiswaKelas::create($validated);
        $anggota->load(['siswa', 'kelas']);

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil ditambahkan ke kelas.',
            'data' => [
                'id' => $anggota->id,
                'siswa_id' => $anggota->siswa_id,
                'nama_siswa' => $anggota->siswa?->nama_siswa,
                'kelas_id' => $anggota->kelas_id,
                'nama_kelas' => $anggota->kelas?->nama_kelas,
                'status' => $anggota->status,
            ],
        ], 201);
    }

    public function destroy(string $id): JsonResponse
    {
        $anggota = SiswaKelas::find($id);

        if (! $anggota) {
            return response()->json([
                'success' => false,
                'message' => 'Data anggota kelas tidak ditemukan.',
            ], 404);
        }

        $anggota->delete();

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil dihapus dari kelas.',
        ]);
    }
}
