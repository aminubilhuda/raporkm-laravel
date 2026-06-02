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
        Pengingat::create($r->validated());

        return back()->with('status', 'Pengingat ditambahkan.');
    }

    public function destroy(Pengingat $pengingat)
    {
        $pengingat->delete();

        return back()->with('status', 'Pengingat dihapus.');
    }
}
