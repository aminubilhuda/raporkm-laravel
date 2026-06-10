<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\Prakerin;
use App\Models\Sekolah;
use App\Models\SiswaPrakerin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrakerinController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;

        $query = Prakerin::withCount('siswaPrakerin as jumlah_siswa')
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId));

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_perusahaan', 'like', "%{$search}%")
                    ->orWhere('PIC', 'like', "%{$search}%");
            });
        }

        $prakerins = $query->latest('id')->get();

        return response()->json([
            'success' => true,
            'data' => $prakerins->map(fn ($p) => [
                'id' => $p->id,
                'nama_perusahaan' => $p->nama_perusahaan,
                'alamat' => $p->alamat,
                'kontak' => $p->kontak,
                'PIC' => $p->PIC,
                'tanggal_mulai' => $p->tanggal_mulai,
                'tanggal_selesai' => $p->tanggal_selesai,
                'keterangan' => $p->keterangan,
                'jumlah_siswa' => $p->jumlah_siswa,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_perusahaan' => ['required', 'string', 'max:200'],
            'alamat' => ['nullable', 'string'],
            'kontak' => ['nullable', 'string', 'max:50'],
            'PIC' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $sekolah = Sekolah::first();
        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $prakerin = Prakerin::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Prakerin berhasil dibuat.',
            'data' => $prakerin,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $prakerin = Prakerin::find($id);

        if (! $prakerin) {
            return response()->json(['success' => false, 'message' => 'Prakerin tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'nama_perusahaan' => ['sometimes', 'string', 'max:200'],
            'alamat' => ['nullable', 'string'],
            'kontak' => ['nullable', 'string', 'max:50'],
            'PIC' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $prakerin->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Prakerin berhasil diperbarui.',
            'data' => $prakerin,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $prakerin = Prakerin::find($id);

        if (! $prakerin) {
            return response()->json(['success' => false, 'message' => 'Prakerin tidak ditemukan.'], 404);
        }

        $prakerin->delete();

        return response()->json(['success' => true, 'message' => 'Prakerin berhasil dihapus.']);
    }

    public function peserta(Request $request): JsonResponse
    {
        $request->validate([
            'prakerin_id' => ['required', 'exists:prakerin,id'],
        ]);

        $peserta = SiswaPrakerin::with(['siswa' => fn ($q) => $q->select('id', 'nama_siswa', 'nisn'), 'kelas' => fn ($q) => $q->select('id', 'nama_kelas'), 'user' => fn ($q) => $q->select('id', 'nama')])
            ->where('prakerin_id', $request->integer('prakerin_id'))
            ->get();

        return response()->json([
            'success' => true,
            'data' => $peserta->map(fn ($p) => [
                'id' => $p->id,
                'siswa_id' => $p->siswa_id,
                'nama_siswa' => $p->siswa?->nama_siswa,
                'nisn' => $p->siswa?->nisn,
                'kelas_id' => $p->kelas_id,
                'nama_kelas' => $p->kelas?->nama_kelas,
                'guru_pembimbing' => $p->user?->nama,
                'status' => $p->status,
            ]),
        ]);
    }

    public function storePeserta(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prakerin_id' => ['required', 'exists:prakerin,id'],
            'siswa_id' => ['required', 'exists:siswa,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $sekolah = Sekolah::first();
        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $peserta = SiswaPrakerin::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Peserta prakerin berhasil ditambahkan.',
            'data' => $peserta,
        ], 201);
    }

    public function destroyPeserta(string $id): JsonResponse
    {
        $peserta = SiswaPrakerin::find($id);

        if (! $peserta) {
            return response()->json(['success' => false, 'message' => 'Peserta tidak ditemukan.'], 404);
        }

        $peserta->delete();

        return response()->json(['success' => true, 'message' => 'Peserta prakerin berhasil dihapus.']);
    }
}
