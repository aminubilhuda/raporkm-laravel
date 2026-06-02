<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Ekstra\StoreEskulRequest;
use App\Http\Requests\TU\Ekstra\StorePembinaEskulRequest;
use App\Http\Requests\TU\Ekstra\UpdateEskulRequest;
use App\Models\Eskul;
use App\Models\PembinaEskul;
use App\Models\User;

class EkstraController extends Controller
{
    public function index()
    {
        $eskuls = Eskul::with('pembinaEskul.user')->latest()->get();
        $gurus = User::where('jabatan', 3)->orderBy('nama')->get();

        return view('tu.ekstra.index', compact('eskuls', 'gurus'));
    }

    public function store(StoreEskulRequest $r)
    {
        Eskul::create($r->validated());

        return back()->with('status', 'Eskul ditambahkan.');
    }

    public function update(UpdateEskulRequest $r, Eskul $eskul)
    {
        $eskul->update($r->validated());

        return back()->with('status', 'Eskul diperbarui.');
    }

    public function destroy(Eskul $eskul)
    {
        $eskul->delete();

        return back()->with('status', 'Eskul dihapus.');
    }

    public function pembinaStore(StorePembinaEskulRequest $r)
    {
        $d = $r->validated();
        if (PembinaEskul::where('eskul_id', $d['eskul_id'])->where('user_id', $d['user_id'])->exists()) {
            return back()->with('error', 'Guru sudah menjadi pembina eskul ini.');
        }
        PembinaEskul::create($d);

        return back()->with('status', 'Pembina ditambahkan.');
    }

    public function pembinaDestroy(PembinaEskul $pembinaEskul)
    {
        $pembinaEskul->delete();

        return back()->with('status', 'Pembina dihapus.');
    }
}
