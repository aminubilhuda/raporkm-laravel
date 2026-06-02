<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\P5bk\StoreDimensiRequest;
use App\Http\Requests\TU\P5bk\StoreElemenRequest;
use App\Http\Requests\TU\P5bk\StoreProyekRequest;
use App\Http\Requests\TU\P5bk\StoreSubElemenRequest;
use App\Http\Requests\TU\P5bk\StoreTemaRequest;
use App\Http\Requests\TU\P5bk\UpdateDimensiRequest;
use App\Http\Requests\TU\P5bk\UpdateElemenRequest;
use App\Http\Requests\TU\P5bk\UpdateProyekRequest;
use App\Http\Requests\TU\P5bk\UpdateSubElemenRequest;
use App\Http\Requests\TU\P5bk\UpdateTemaRequest;
use App\Models\Dimensi;
use App\Models\Elemen;
use App\Models\Kelas;
use App\Models\ProyekKelas;
use App\Models\ProyekTema;
use App\Models\SubElemen;
use App\Models\User;

class P5bkController extends Controller
{
    public function dimensi()
    {
        $dimensi = Dimensi::orderBy('urutan')->get();
        $elemens = Elemen::with('dimensi')->orderBy('urutan')->get();
        $subs = SubElemen::with('elemen.dimensi')->orderBy('urutan')->get();

        return view('tu.p5bk.index', compact('dimensi', 'elemens', 'subs'));
    }

    public function dimensiStore(StoreDimensiRequest $r)
    {
        Dimensi::create($r->validated());

        return back()->with('status', 'Dimensi ditambahkan.');
    }

    public function dimensiUpdate(UpdateDimensiRequest $r, Dimensi $dimensi)
    {
        $dimensi->update($r->validated());

        return back()->with('status', 'Dimensi diperbarui.');
    }

    public function dimensiDestroy(Dimensi $dimensi)
    {
        $dimensi->delete();

        return back()->with('status', 'Dimensi dihapus.');
    }

    public function elemenStore(StoreElemenRequest $r)
    {
        Elemen::create($r->validated());

        return back()->with('status', 'Elemen ditambahkan.');
    }

    public function elemenUpdate(UpdateElemenRequest $r, Elemen $elemen)
    {
        $elemen->update($r->validated());

        return back()->with('status', 'Elemen diperbarui.');
    }

    public function elemenDestroy(Elemen $elemen)
    {
        $elemen->delete();

        return back()->with('status', 'Elemen dihapus.');
    }

    public function subStore(StoreSubElemenRequest $r)
    {
        SubElemen::create($r->validated());

        return back()->with('status', 'Sub-elemen ditambahkan.');
    }

    public function subUpdate(UpdateSubElemenRequest $r, SubElemen $subElemen)
    {
        $subElemen->update($r->validated());

        return back()->with('status', 'Sub-elemen diperbarui.');
    }

    public function subDestroy(SubElemen $subElemen)
    {
        $subElemen->delete();

        return back()->with('status', 'Sub-elemen dihapus.');
    }

    public function tema()
    {
        $temas = ProyekTema::latest()->get();

        return view('tu.p5bk.tema', compact('temas'));
    }

    public function temaStore(StoreTemaRequest $r)
    {
        ProyekTema::create($r->validated());

        return back()->with('status', 'Tema ditambahkan.');
    }

    public function temaUpdate(UpdateTemaRequest $r, ProyekTema $proyekTema)
    {
        $proyekTema->update($r->validated());

        return back()->with('status', 'Tema diperbarui.');
    }

    public function temaDestroy(ProyekTema $proyekTema)
    {
        $proyekTema->delete();

        return back()->with('status', 'Tema dihapus.');
    }

    public function proyek()
    {
        $proyeks = ProyekKelas::with(['kelas', 'proyekTema', 'user'])->latest()->paginate(15);
        $kelass = Kelas::orderBy('nama_kelas')->get();
        $temas = ProyekTema::latest()->get();
        $gurus = User::where('jabatan', 3)->orderBy('nama')->get();

        return view('tu.p5bk.proyek', compact('proyeks', 'kelass', 'temas', 'gurus'));
    }

    public function proyekStore(StoreProyekRequest $r)
    {
        ProyekKelas::create($r->validated());

        return back()->with('status', 'Proyek ditambahkan.');
    }

    public function proyekUpdate(UpdateProyekRequest $r, ProyekKelas $proyekKelas)
    {
        $proyekKelas->update($r->validated());

        return back()->with('status', 'Proyek diperbarui.');
    }

    public function proyekDestroy(ProyekKelas $proyekKelas)
    {
        $proyekKelas->delete();

        return back()->with('status', 'Proyek dihapus.');
    }
}
