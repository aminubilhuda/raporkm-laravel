<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Pengingat\StorePengingatRequest;
use App\Models\Pengingat;

class PengingatController extends Controller
{
    public function index()
    {
        $pengingat = Pengingat::latest()->get();

        return view('tu.pengingat.index', compact('pengingat'));
    }

    public function store(StorePengingatRequest $r)
    {
        $p = Pengingat::create($r->validated());

        activity()->performedOn($p)->event('created')
            ->withProperties(['judul' => $p->judul])
            ->log('Pengingat ditambahkan');

        return back()->with('status', 'Pengingat ditambahkan.');
    }

    public function destroy(Pengingat $pengingat)
    {
        $judul = $pengingat->judul;
        $pengingat->delete();

        activity()->performedOn($pengingat)->event('deleted')
            ->withProperties(['judul' => $judul])
            ->log('Pengingat dihapus');

        return back()->with('status', 'Pengingat dihapus.');
    }
}
