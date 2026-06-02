<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\KelompokMapel;
use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index()
    {
        $mapel = Mapel::with('kelompokMapel')->latest()->paginate(20);
        $kelompok = KelompokMapel::orderBy('nama')->get();

        return view('tu.mapel.index', compact('mapel', 'kelompok'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelompok_mapel_id' => ['required', 'exists:kelompok_mapel,id'],
            'kode' => ['nullable', 'string', 'max:20', 'unique:mapel,kode'],
            'nama_mapel' => ['required', 'string', 'max:200'],
            'kkm' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        Mapel::create($validated);

        return back()->with('status', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, Mapel $mapel)
    {
        $validated = $request->validate([
            'kelompok_mapel_id' => ['required', 'exists:kelompok_mapel,id'],
            'kode' => ['nullable', 'string', 'max:20', 'unique:mapel,kode,'.$mapel->id],
            'nama_mapel' => ['required', 'string', 'max:200'],
            'kkm' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $mapel->update($validated);

        return back()->with('status', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Mapel $mapel)
    {
        $mapel->delete();

        return back()->with('status', 'Mata pelajaran berhasil dihapus.');
    }
}
