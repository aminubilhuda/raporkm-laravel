<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\MapelKelas as MapelKelasModel;
use App\Models\MapelSiswa as MapelSiswaModel;
use App\Models\Siswa;
use Illuminate\Http\Request;

class MapelSiswaController extends Controller
{
    public function index(Request $request)
    {
        $kelasId = $request->get('kelas_id');

        $mapelKelass = MapelKelasModel::with(['kelas', 'mapel'])->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId))->get();
        $siswas = Siswa::where('aktif', 1)->orderBy('nama_siswa')->get();
        $assignments = collect();
        $mapelKelasId = $request->get('mapel_kelas_id');

        if ($mapelKelasId) {
            $assignments = MapelSiswaModel::with('siswa')
                ->where('mapel_kelas_id', $mapelKelasId)
                ->get();
        }

        return view('tu.mapel-siswa.index', compact('mapelKelass', 'siswas', 'assignments', 'mapelKelasId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mapel_kelas_id' => ['required', 'exists:mapel_kelas,id'],
            'siswa_id' => ['required', 'exists:siswa,id'],
        ]);

        $exists = MapelSiswaModel::where('mapel_kelas_id', $validated['mapel_kelas_id'])
            ->where('siswa_id', $validated['siswa_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Siswa sudah terdaftar di mapel ini.');
        }

        MapelSiswaModel::create($validated);

        return back()->with('status', 'Siswa berhasil ditambahkan ke mapel.');
    }

    public function destroy($id)
    {
        MapelSiswaModel::findOrFail($id)->delete();

        return back()->with('status', 'Siswa berhasil dihapus dari mapel.');
    }
}
