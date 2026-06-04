<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\MapelKelas as MapelKelasModel;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;

class MapelKelasController extends Controller
{
    public function index(Request $request)
    {
        $kelasId = $request->get('kelas_id');

        $kelass = Kelas::with('tingkat', 'kompetensiKeahlian')->orderBy('nama_kelas')->get();
        $mapels = Mapel::orderBy('nama_mapel')->get();
        $gurus = User::where('jabatan', 3)->orderBy('nama')->get();
        $assignments = collect();

        if ($kelasId) {
            $assignments = MapelKelasModel::with(['mapel', 'user'])
                ->where('kelas_id', $kelasId)
                ->whereNull('deleted_at')
                ->get();
        }

        return view('tu.mapel-kelas.index', compact('kelass', 'mapels', 'gurus', 'assignments', 'kelasId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mapel_id' => ['required', 'exists:mapel,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'kkm' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $exists = MapelKelasModel::where('kelas_id', $validated['kelas_id'])
            ->where('mapel_id', $validated['mapel_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Mapel ini sudah di-assign ke kelas tersebut.');
        }

        $sekolah = Sekolah::first();

        MapelKelasModel::create([
            'kelas_id' => $validated['kelas_id'],
            'mapel_id' => $validated['mapel_id'],
            'user_id' => $validated['user_id'] ?? null,
            'kkm' => $validated['kkm'] ?? 75,
            'tahun_pelajaran_id' => $request->tahun_pelajaran_id ?? $sekolah?->tahun_aktif,
            'semester_id' => $request->semester_id ?? $sekolah?->semester_aktif,
        ]);

        return back()->with('status', 'Mapel berhasil di-assign ke kelas.');
    }

    public function destroy($id)
    {
        MapelKelasModel::findOrFail($id)->delete();

        return back()->with('status', 'Assign mapel berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        $mapelKelas = MapelKelasModel::findOrFail($id);

        $validated = $request->validate([
            'mapel_id' => ['required', 'exists:mapel,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'kkm' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $mapelKelas->update($validated);

        return back()->with('status', 'Mapel berhasil diperbarui.');
    }

    public function updateBatch(Request $request)
    {
        $data = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'assignments' => ['required', 'array'],
            'assignments.*.id' => ['required', 'exists:mapel_kelas,id'],
            'assignments.*.user_id' => ['nullable', 'exists:users,id'],
            'assignments.*.kkm' => ['nullable', 'integer', 'min:0', 'max:100'],
            'assignments.*.urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $updated = 0;
        foreach ($data['assignments'] as $item) {
            $mk = MapelKelasModel::find($item['id']);
            if ($mk && (int) $mk->kelas_id === (int) $data['kelas_id']) {
                $mk->update([
                    'user_id' => $item['user_id'] ?? null,
                    'kkm' => $item['kkm'] ?? $mk->kkm,
                ]);
                $updated++;
            }
        }

        return back()->with('status', "{$updated} data berhasil disimpan.");
    }
}
