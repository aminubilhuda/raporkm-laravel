<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Prakerin;

class PrakerinController extends Controller
{
    public function index()
    {
        $prakerins = Prakerin::with('tahunPelajaran', 'semester')
            ->latest()
            ->paginate(15);

        return view('guru.prakerin.index', compact('prakerins'));
    }
}
