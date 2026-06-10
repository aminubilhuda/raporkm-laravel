<?php

namespace App\Http\Controllers\Api\V1\Guru;

use App\Http\Controllers\Controller;
use App\Models\NilaiProyek;
use App\Models\ProyekKelas;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class P5bkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $proyek = ProyekKelas::with(['proyekTema', 'kelas'])
            ->where('user_id', $user->id)
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->get();

        return response()->json([
            'success' => true,
            'data' => $proyek->map(fn ($p) => [
                'id' => $p->id,
                'tema' => $p->proyekTema?->nama_tema,
                'kelas' => $p->kelas?->nama_kelas,
                'judul' => $p->judul,
                'deskripsi' => $p->deskripsi,
            ]),
        ]);
    }

    public function penilaian(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $request->validate([
            'proyek_kelas_id' => ['required', 'exists:proyek_kelas,id'],
        ]);

        $proyekKelasId = $request->integer('proyek_kelas_id');

        $siswaIds = SiswaKelas::where('kelas_id', ProyekKelas::find($proyekKelasId)?->kelas_id)
            ->where('status', 'aktif')
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->pluck('siswa_id');

        $nilai = NilaiProyek::where('proyek_kelas_id', $proyekKelasId)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->groupBy('siswa_id');

        $siswa = Siswa::whereIn('id', $siswaIds)->select('id', 'nama_siswa')->get();

        return response()->json([
            'success' => true,
            'data' => $siswa->map(fn ($s) => [
                'siswa_id' => $s->id,
                'nama_siswa' => $s->nama_siswa,
                'nilai' => $nilai->get($s->id)?->map(fn ($n) => [
                    'dimensi_id' => $n->dimensi_id,
                    'elemen_id' => $n->elemen_id,
                    'nilai' => $n->nilai,
                    'deskripsi' => $n->deskripsi,
                ]),
            ]),
        ]);
    }

    public function storePenilaian(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();

        $validated = $request->validate([
            'proyek_kelas_id' => ['required', 'exists:proyek_kelas,id'],
            'siswa_id' => ['required', 'exists:siswa,id'],
            'dimensi_id' => ['required', 'exists:dimensi,id'],
            'elemen_id' => ['nullable', 'exists:elemen,id'],
            'nilai' => ['required', 'string', 'max:30'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $nilai = NilaiProyek::updateOrCreate(
            [
                'proyek_kelas_id' => $validated['proyek_kelas_id'],
                'siswa_id' => $validated['siswa_id'],
                'dimensi_id' => $validated['dimensi_id'],
                'tahun_pelajaran_id' => $validated['tahun_pelajaran_id'],
                'semester_id' => $validated['semester_id'],
            ],
            [
                'elemen_id' => $validated['elemen_id'] ?? null,
                'nilai' => $validated['nilai'],
                'deskripsi' => $validated['deskripsi'] ?? null,
            ],
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai proyek berhasil disimpan.',
            'data' => $nilai,
        ], 201);
    }
}
