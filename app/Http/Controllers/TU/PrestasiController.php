<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Prestasi\StorePrestasiRequest;
use App\Http\Requests\TU\Prestasi\UpdatePrestasiRequest;
use App\Models\Prestasi;
use App\Models\Siswa;

class PrestasiController extends Controller
{
    public function index()
    {
        $prestasis = Prestasi::with('siswa')->latest()->paginate(15);
        $siswas = Siswa::where('aktif', 1)->orderBy('nama_siswa')->get();

        return view('tu.prestasi.index', compact('prestasis', 'siswas'));
    }

    public function store(StorePrestasiRequest $r)
    {
        Prestasi::create($r->validated());

        return back()->with('status', 'Prestasi ditambahkan.');
    }

    public function update(UpdatePrestasiRequest $r, Prestasi $prestasi)
    {
        $prestasi->update($r->validated());

        return back()->with('status', 'Diperbarui.');
    }

    public function destroy(Prestasi $prestasi)
    {
        $prestasi->delete();

        return back()->with('status', 'Dihapus.');
    }
}
