<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\PiketHarian\StorePiketHarianRequest;
use App\Models\PiketHarian;
use App\Models\User;

class PiketHarianController extends Controller
{
    public function index()
    {
        $pikets = PiketHarian::with(['user', 'hari'])->latest()->get();
        $gurus = User::where('jabatan', 3)->orderBy('nama')->get();

        return view('tu.piket-harian.index', compact('pikets', 'gurus'));
    }

    public function store(StorePiketHarianRequest $r)
    {
        PiketHarian::create($r->validated());

        return back()->with('status', 'Piket ditambahkan.');
    }

    public function destroy(PiketHarian $piketHarian)
    {
        $piketHarian->delete();

        return back()->with('status', 'Piket dihapus.');
    }
}
