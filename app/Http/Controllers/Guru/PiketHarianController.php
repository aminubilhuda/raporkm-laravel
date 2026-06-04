<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\PiketHarian;

class PiketHarianController extends Controller
{
    public function index()
    {
        $pikets = PiketHarian::with('user', 'hari')
            ->latest()
            ->get();

        return view('guru.piket-harian.index', compact('pikets'));
    }
}
