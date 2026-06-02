<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\KelompokMapel;
use Illuminate\Http\Request;

class KelompokMapelController extends Controller
{
    public function index()
    {
        $kelompok = KelompokMapel::latest()->get();

        return view('tu.kelompok-mapel.index', compact('kelompok'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50', 'unique:kelompok_mapel,nama'],
            'keterangan' => ['nullable', 'string'],
        ]);

        KelompokMapel::create($validated);

        return back()->with('status', 'Kelompok mapel berhasil ditambahkan.');
    }

    public function update(Request $request, KelompokMapel $kelompokMapel)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50', 'unique:kelompok_mapel,nama,'.$kelompokMapel->id],
            'keterangan' => ['nullable', 'string'],
        ]);

        $kelompokMapel->update($validated);

        return back()->with('status', 'Kelompok mapel berhasil diperbarui.');
    }

    public function destroy(KelompokMapel $kelompokMapel)
    {
        $kelompokMapel->delete();

        return back()->with('status', 'Kelompok mapel berhasil dihapus.');
    }
}
