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
use App\Models\Sekolah;
use App\Models\SubElemen;
use App\Models\User;
use Illuminate\Http\Request;

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
        $dimensi = Dimensi::create($r->validated());

        activity()->performedOn($dimensi)->event('created')
            ->withProperties(['nama' => $dimensi->nama])
            ->log('Dimensi P5BK ditambahkan');

        return back()->with('status', 'Dimensi ditambahkan.');
    }

    public function dimensiUpdate(UpdateDimensiRequest $r, Dimensi $dimensi)
    {
        $dimensi->update($r->validated());

        activity()->performedOn($dimensi)->event('updated')
            ->withProperties(['nama' => $dimensi->nama])
            ->log('Dimensi P5BK diperbarui');

        return back()->with('status', 'Dimensi diperbarui.');
    }

    public function dimensiDestroy(Dimensi $dimensi)
    {
        $nama = $dimensi->nama;
        $dimensi->delete();

        activity()->performedOn($dimensi)->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Dimensi P5BK dihapus');

        return back()->with('status', 'Dimensi dihapus.');
    }

    public function elemenStore(StoreElemenRequest $r)
    {
        $elemen = Elemen::create($r->validated());

        activity()->performedOn($elemen)->event('created')
            ->withProperties(['nama' => $elemen->nama])
            ->log('Elemen P5BK ditambahkan');

        return back()->with('status', 'Elemen ditambahkan.');
    }

    public function elemenUpdate(UpdateElemenRequest $r, Elemen $elemen)
    {
        $elemen->update($r->validated());

        activity()->performedOn($elemen)->event('updated')
            ->withProperties(['nama' => $elemen->nama])
            ->log('Elemen P5BK diperbarui');

        return back()->with('status', 'Elemen diperbarui.');
    }

    public function elemenDestroy(Elemen $elemen)
    {
        $nama = $elemen->nama;
        $elemen->delete();

        activity()->performedOn($elemen)->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Elemen P5BK dihapus');

        return back()->with('status', 'Elemen dihapus.');
    }

    public function subStore(StoreSubElemenRequest $r)
    {
        $sub = SubElemen::create($r->validated());

        activity()->performedOn($sub)->event('created')
            ->withProperties(['nama' => $sub->nama])
            ->log('Sub-elemen P5BK ditambahkan');

        return back()->with('status', 'Sub-elemen ditambahkan.');
    }

    public function subUpdate(UpdateSubElemenRequest $r, SubElemen $subElemen)
    {
        $subElemen->update($r->validated());

        activity()->performedOn($subElemen)->event('updated')
            ->withProperties(['nama' => $subElemen->nama])
            ->log('Sub-elemen P5BK diperbarui');

        return back()->with('status', 'Sub-elemen diperbarui.');
    }

    public function subDestroy(SubElemen $subElemen)
    {
        $nama = $subElemen->nama;
        $subElemen->delete();

        activity()->performedOn($subElemen)->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Sub-elemen P5BK dihapus');

        return back()->with('status', 'Sub-elemen dihapus.');
    }

    public function tema(Request $request)
    {
        $sekolah = Sekolah::first();
        $tpId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        $query = ProyekTema::where('tahun_pelajaran_id', $tpId)
            ->where('semester_id', $semesterId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_tema', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $temas = $query->latest()->get();
        } else {
            $temas = $query->latest()->paginate((int) $perPage)->withQueryString();
        }

        return view('tu.p5bk.tema', compact('temas'));
    }

    public function temaStore(StoreTemaRequest $r)
    {
        $sekolah = Sekolah::first();
        $data = $r->validated();
        $data['tahun_pelajaran_id'] = $data['tahun_pelajaran_id'] ?? session('selected_tahun', $sekolah?->tahun_aktif);
        $data['semester_id'] = $data['semester_id'] ?? session('selected_semester', $sekolah?->semester_aktif);

        $tema = ProyekTema::create($data);

        activity()
            ->performedOn($tema)
            ->event('created')
            ->withProperties(['nama' => $tema->nama_tema])
            ->log('Tema P5 ditambahkan');

        return back()->with('status', 'Tema ditambahkan.');
    }

    public function temaUpdate(UpdateTemaRequest $r, ProyekTema $proyekTema)
    {
        $proyekTema->update($r->validated());

        activity()
            ->performedOn($proyekTema)
            ->event('updated')
            ->withProperties(['nama' => $proyekTema->nama_tema])
            ->log('Tema P5 diperbarui');

        return back()->with('status', 'Tema diperbarui.');
    }

    public function temaDestroy(ProyekTema $proyekTema)
    {
        $nama = $proyekTema->nama_tema;
        $proyekTema->delete();

        activity()
            ->performedOn($proyekTema)
            ->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Tema P5 dihapus');

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
