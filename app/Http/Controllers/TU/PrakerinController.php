<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Prakerin\StorePrakerinRequest;
use App\Http\Requests\TU\Prakerin\StoreSiswaPrakerinRequest;
use App\Http\Requests\TU\Prakerin\UpdatePrakerinRequest;
use App\Models\Kelas;
use App\Models\Prakerin;
use App\Models\Siswa;
use App\Models\SiswaPrakerin;
use App\Models\User;
use Illuminate\Http\Request;

class PrakerinController extends Controller
{
    public function index()
    {
        $prakerins = Prakerin::latest()->paginate(15);
        $kelass = Kelas::orderBy('nama_kelas')->get();

        return view('tu.prakerin.index', compact('prakerins', 'kelass'));
    }

    public function store(StorePrakerinRequest $r)
    {
        Prakerin::create($r->validated());

        return back()->with('status', 'Prakerin ditambahkan.');
    }

    public function update(UpdatePrakerinRequest $r, Prakerin $prakerin)
    {
        $prakerin->update($r->validated());

        return back()->with('status', 'Prakerin diperbarui.');
    }

    public function destroy(Prakerin $prakerin)
    {
        $prakerin->delete();

        return back()->with('status', 'Prakerin dihapus.');
    }

    public function peserta(Request $r)
    {
        $prakerinId = $r->get('prakerin_id');
        $peserta = collect();
        $siswas = Siswa::where('aktif', 1)->orderBy('nama_siswa')->get();
        $gurus = User::where('jabatan', 3)->orderBy('nama')->get();
        if ($prakerinId) {
            $peserta = SiswaPrakerin::with(['siswa', 'kelas', 'user'])->where('prakerin_id', $prakerinId)->get();
        }

        return view('tu.prakerin.peserta', compact('prakerinId', 'peserta', 'siswas', 'gurus'));
    }

    public function pesertaStore(StoreSiswaPrakerinRequest $r)
    {
        SiswaPrakerin::create($r->validated());

        return back()->with('status', 'Peserta ditambahkan.');
    }

    public function pesertaDestroy(SiswaPrakerin $siswaPrakerin)
    {
        $siswaPrakerin->delete();

        return back()->with('status', 'Peserta dihapus.');
    }
}
