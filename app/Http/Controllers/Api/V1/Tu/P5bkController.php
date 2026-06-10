<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\Dimensi;
use App\Models\Elemen;
use App\Models\ProyekKelas;
use App\Models\ProyekTema;
use App\Models\Sekolah;
use App\Models\SubElemen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class P5bkController extends Controller
{
    // ── Proyek Tema ──

    public function index(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $query = ProyekTema::withCount('proyekKelas as jumlah_kelas')
            ->when($tpId, fn ($q) => $q->where('tahun_pelajaran_id', $tpId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId));

        if ($request->filled('search')) {
            $query->where('nama_tema', 'like', "%{$request->input('search')}%");
        }

        $tema = $query->latest('id')->get();

        return response()->json([
            'success' => true,
            'data' => $tema->map(fn ($t) => [
                'id' => $t->id,
                'nama_tema' => $t->nama_tema,
                'keterangan' => $t->keterangan,
                'jumlah_kelas' => $t->jumlah_kelas,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_tema' => ['required', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $sekolah = Sekolah::first();
        $validated['tahun_pelajaran_id'] = $sekolah?->tahun_aktif;
        $validated['semester_id'] = $sekolah?->semester_aktif;

        $tema = ProyekTema::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tema P5BK berhasil dibuat.',
            'data' => $tema,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $tema = ProyekTema::find($id);

        if (! $tema) {
            return response()->json(['success' => false, 'message' => 'Tema tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'nama_tema' => ['sometimes', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $tema->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tema P5BK berhasil diperbarui.',
            'data' => $tema,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $tema = ProyekTema::find($id);

        if (! $tema) {
            return response()->json(['success' => false, 'message' => 'Tema tidak ditemukan.'], 404);
        }

        $tema->delete();

        return response()->json(['success' => true, 'message' => 'Tema P5BK berhasil dihapus.']);
    }

    public function proyek_kelas(Request $request): JsonResponse
    {
        $request->validate([
            'proyek_tema_id' => ['nullable', 'exists:proyek_tema,id'],
        ]);

        $sekolah = Sekolah::first();
        $tpId = $sekolah?->tahun_aktif;
        $semId = $sekolah?->semester_aktif;

        $proyek = ProyekKelas::with(['kelas', 'user', 'proyekTema'])
            ->when($request->filled('proyek_tema_id'), fn ($q) => $q->where('proyek_tema_id', $request->integer('proyek_tema_id')))
            ->when($tpId, function ($q) use ($tpId) {
                $q->whereHas('proyekTema', function ($pt) use ($tpId) {
                    $pt->where('tahun_pelajaran_id', $tpId);
                });
            })
            ->when($semId, function ($q) use ($semId) {
                $q->whereHas('proyekTema', function ($pt) use ($semId) {
                    $pt->where('semester_id', $semId);
                });
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => $proyek->map(fn ($p) => [
                'id' => $p->id,
                'kelas_id' => $p->kelas_id,
                'nama_kelas' => $p->kelas?->nama_kelas,
                'judul' => $p->judul,
                'deskripsi' => $p->deskripsi,
                'guru' => $p->user?->nama,
                'tema' => $p->proyekTema?->nama_tema,
            ]),
        ]);
    }

    // ── Dimensi CRUD ──

    public function dimensiIndex(): JsonResponse
    {
        $data = Dimensi::withCount('elemens')->orderBy('urutan')->get();

        return response()->json([
            'success' => true,
            'data' => $data->map(fn ($d) => [
                'id' => $d->id,
                'nama' => $d->nama,
                'keterangan' => $d->keterangan,
                'urutan' => $d->urutan,
                'jumlah_elemen' => $d->elemens_count,
            ]),
        ]);
    }

    public function dimensiStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $dimensi = Dimensi::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dimensi berhasil dibuat.',
            'data' => $dimensi,
        ], 201);
    }

    public function dimensiUpdate(Request $request, string $id): JsonResponse
    {
        $dimensi = Dimensi::find($id);

        if (! $dimensi) {
            return response()->json(['success' => false, 'message' => 'Dimensi tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'nama' => ['sometimes', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $dimensi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dimensi berhasil diperbarui.',
            'data' => $dimensi,
        ]);
    }

    public function dimensiDestroy(string $id): JsonResponse
    {
        $dimensi = Dimensi::find($id);

        if (! $dimensi) {
            return response()->json(['success' => false, 'message' => 'Dimensi tidak ditemukan.'], 404);
        }

        $dimensi->delete();

        return response()->json(['success' => true, 'message' => 'Dimensi berhasil dihapus.']);
    }

    // ── Elemen CRUD ──

    public function elemenIndex(Request $request): JsonResponse
    {
        $request->validate([
            'dimensi_id' => ['required', 'exists:dimensi,id'],
        ]);

        $data = Elemen::withCount('subElemens')
            ->where('dimensi_id', $request->integer('dimensi_id'))
            ->orderBy('urutan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data->map(fn ($e) => [
                'id' => $e->id,
                'dimensi_id' => $e->dimensi_id,
                'nama' => $e->nama,
                'keterangan' => $e->keterangan,
                'urutan' => $e->urutan,
                'jumlah_sub_elemen' => $e->sub_elemens_count,
            ]),
        ]);
    }

    public function elemenStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dimensi_id' => ['required', 'exists:dimensi,id'],
            'nama' => ['required', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $elemen = Elemen::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Elemen berhasil dibuat.',
            'data' => $elemen,
        ], 201);
    }

    public function elemenUpdate(Request $request, string $id): JsonResponse
    {
        $elemen = Elemen::find($id);

        if (! $elemen) {
            return response()->json(['success' => false, 'message' => 'Elemen tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'nama' => ['sometimes', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $elemen->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Elemen berhasil diperbarui.',
            'data' => $elemen,
        ]);
    }

    public function elemenDestroy(string $id): JsonResponse
    {
        $elemen = Elemen::find($id);

        if (! $elemen) {
            return response()->json(['success' => false, 'message' => 'Elemen tidak ditemukan.'], 404);
        }

        $elemen->delete();

        return response()->json(['success' => true, 'message' => 'Elemen berhasil dihapus.']);
    }

    // ── SubElemen CRUD ──

    public function subElemenIndex(Request $request): JsonResponse
    {
        $request->validate([
            'elemen_id' => ['required', 'exists:elemen,id'],
        ]);

        $data = SubElemen::where('elemen_id', $request->integer('elemen_id'))
            ->orderBy('urutan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data->map(fn ($s) => [
                'id' => $s->id,
                'elemen_id' => $s->elemen_id,
                'nama' => $s->nama,
                'capaian' => $s->capaian,
                'urutan' => $s->urutan,
            ]),
        ]);
    }

    public function subElemenStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'elemen_id' => ['required', 'exists:elemen,id'],
            'nama' => ['required', 'string'],
            'capaian' => ['nullable', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $sub = SubElemen::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sub elemen berhasil dibuat.',
            'data' => $sub,
        ], 201);
    }

    public function subElemenUpdate(Request $request, string $id): JsonResponse
    {
        $sub = SubElemen::find($id);

        if (! $sub) {
            return response()->json(['success' => false, 'message' => 'Sub elemen tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'nama' => ['sometimes', 'string'],
            'capaian' => ['nullable', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $sub->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sub elemen berhasil diperbarui.',
            'data' => $sub,
        ]);
    }

    public function subElemenDestroy(string $id): JsonResponse
    {
        $sub = SubElemen::find($id);

        if (! $sub) {
            return response()->json(['success' => false, 'message' => 'Sub elemen tidak ditemukan.'], 404);
        }

        $sub->delete();

        return response()->json(['success' => true, 'message' => 'Sub elemen berhasil dihapus.']);
    }
}
