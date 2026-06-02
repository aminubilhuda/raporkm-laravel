<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\MapelKelas as MapelKelasModel;
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
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $exists = MapelKelasModel::where('kelas_id', $validated['kelas_id'])
            ->where('mapel_id', $validated['mapel_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Mapel ini sudah di-assign ke kelas tersebut.');
        }

        MapelKelasModel::create([
            'kelas_id' => $validated['kelas_id'],
            'mapel_id' => $validated['mapel_id'],
            'user_id' => $validated['user_id'],
            'tahun_pelajaran_id' => $request->tahun_pelajaran_id ?? 2,
            'semester_id' => $request->semester_id ?? 2,
        ]);

        return back()->with('status', 'Mapel berhasil di-assign ke kelas.');
    }

    public function destroy($id)
    {
        MapelKelasModel::findOrFail($id)->delete();

        return back()->with('status', 'Assign mapel berhasil dihapus.');
    }
}
