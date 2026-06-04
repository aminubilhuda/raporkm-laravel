<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\Sekolah;

class EkstraController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = $sekolah?->tahun_aktif;

        $eskuls = Eskul::whereHas('pembinaEskul', fn ($q) => $q
            ->where('user_id', $user->id)
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
        )
            ->with('pembinaEskul.user')
            ->latest()
            ->get();

        return view('guru.ekstra.index', compact('eskuls'));
    }
}
