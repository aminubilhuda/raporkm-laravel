<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Mutasi\StoreMutasiKeluarRequest;
use App\Http\Requests\TU\Mutasi\StoreMutasiMasukRequest;
use App\Models\Kelas;
use App\Models\MutasiKeluar;
use App\Models\MutasiMasuk;
use App\Models\Siswa;

class MutasiController extends Controller
{
    public function masuk()
    {
        $mutasi = MutasiMasuk::with(['siswa', 'kelas'])->latest()->paginate(15);
        $siswas = Siswa::where('aktif', 1)->orderBy('nama_siswa')->get();
        $kelass = Kelas::orderBy('nama_kelas')->get();

        return view('tu.mutasi.masuk', compact('mutasi', 'siswas', 'kelass'));
    }

    public function masukStore(StoreMutasiMasukRequest $r)
    {
        MutasiMasuk::create($r->validated());

        return back()->with('status', 'Mutasi masuk dicatat.');
    }

    public function masukDestroy(MutasiMasuk $mutasiMasuk)
    {
        $mutasiMasuk->delete();

        return back()->with('status', 'Dihapus.');
    }

    public function keluar()
    {
        $mutasi = MutasiKeluar::with(['siswa', 'kelas'])->latest()->paginate(15);
        $siswas = Siswa::where('aktif', 1)->orderBy('nama_siswa')->get();
        $kelass = Kelas::orderBy('nama_kelas')->get();

        return view('tu.mutasi.keluar', compact('mutasi', 'siswas', 'kelass'));
    }

    public function keluarStore(StoreMutasiKeluarRequest $r)
    {
        MutasiKeluar::create($r->validated());

        return back()->with('status', 'Mutasi keluar dicatat.');
    }

    public function keluarDestroy(MutasiKeluar $mutasiKeluar)
    {
        $mutasiKeluar->delete();

        return back()->with('status', 'Dihapus.');
    }
}
