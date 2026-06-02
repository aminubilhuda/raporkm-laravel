<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Lulusan\StoreLulusanRequest;
use App\Http\Requests\TU\Lulusan\UpdateLulusanRequest;
use App\Models\Kelas;
use App\Models\Lulusan;
use App\Models\Siswa;

class LulusanController extends Controller
{
    public function index()
    {
        $lulusans = Lulusan::with(['siswa', 'kelas'])->latest()->paginate(15);
        $siswas = Siswa::where('aktif', 1)->orderBy('nama_siswa')->get();
        $kelass = Kelas::orderBy('nama_kelas')->get();

        return view('tu.lulusan.index', compact('lulusans', 'siswas', 'kelass'));
    }

    public function store(StoreLulusanRequest $r)
    {
        Lulusan::create($r->validated());

        return back()->with('status', 'Data kelulusan ditambahkan.');
    }

    public function update(UpdateLulusanRequest $r, Lulusan $lulusan)
    {
        $lulusan->update($r->validated());

        return back()->with('status', 'Diperbarui.');
    }

    public function destroy(Lulusan $lulusan)
    {
        $lulusan->delete();

        return back()->with('status', 'Dihapus.');
    }
}
