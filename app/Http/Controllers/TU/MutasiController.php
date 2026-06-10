<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Mutasi\StoreMutasiKeluarRequest;
use App\Http\Requests\TU\Mutasi\StoreMutasiMasukRequest;
use App\Models\Kelas;
use App\Models\MutasiKeluar;
use App\Models\MutasiMasuk;
use App\Models\RefJenisKeluar;
use App\Models\Sekolah;
use App\Models\Siswa;
use Illuminate\Http\Request;

class MutasiController extends Controller
{
    public function masuk(Request $request)
    {
        $sekolah = Sekolah::first();
        $tpId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        $query = MutasiMasuk::with(['siswa', 'kelas'])
            ->where('tahun_pelajaran_id', $tpId)
            ->where('semester_id', $semesterId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('siswa', function ($qs) use ($search) {
                    $qs->where('nama_siswa', 'like', "%{$search}%");
                })->orWhere('asal_sekolah', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $mutasi = $query->latest()->get();
        } else {
            $mutasi = $query->latest()->paginate((int) $perPage)->withQueryString();
        }

        $siswas = Siswa::where('aktif', 1)->orderBy('nama_siswa')->get();
        $kelass = Kelas::orderBy('nama_kelas')->get();

        return view('tu.mutasi.masuk', compact('mutasi', 'siswas', 'kelass'));
    }

    public function masukStore(StoreMutasiMasukRequest $r)
    {
        $mutasi = MutasiMasuk::create($r->validated());

        activity()
            ->performedOn($mutasi)
            ->event('created')
            ->withProperties(['nama' => $mutasi->siswa->nama_siswa ?? ''])
            ->log('Mutasi masuk dicatat');

        return back()->with('status', 'Mutasi masuk dicatat.');
    }

    public function masukDestroy(MutasiMasuk $mutasiMasuk)
    {
        $mutasiMasuk->delete();

        return back()->with('status', 'Dihapus.');
    }

    public function keluar(Request $request)
    {
        $sekolah = Sekolah::first();
        $tpId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        $query = MutasiKeluar::with(['siswa', 'kelas', 'jenisKeluar'])
            ->where('tahun_pelajaran_id', $tpId)
            ->where('semester_id', $semesterId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('siswa', function ($qs) use ($search) {
                    $qs->where('nama_siswa', 'like', "%{$search}%");
                })->orWhere('tujuan_sekolah', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $mutasi = $query->latest()->get();
        } else {
            $mutasi = $query->latest()->paginate((int) $perPage)->withQueryString();
        }

        $siswas = Siswa::where('aktif', 1)->orderBy('nama_siswa')->get();
        $kelass = Kelas::orderBy('nama_kelas')->get();
        $jenisKeluars = RefJenisKeluar::orderBy('nama')->get();

        return view('tu.mutasi.keluar', compact('mutasi', 'siswas', 'kelass', 'jenisKeluars'));
    }

    public function keluarStore(StoreMutasiKeluarRequest $r)
    {
        $mutasi = MutasiKeluar::create($r->validated());

        activity()
            ->performedOn($mutasi)
            ->event('created')
            ->withProperties(['nama' => $mutasi->siswa->nama_siswa ?? ''])
            ->log('Mutasi keluar dicatat');

        return back()->with('status', 'Mutasi keluar dicatat.');
    }

    public function keluarDestroy(MutasiKeluar $mutasiKeluar)
    {
        $mutasiKeluar->delete();

        return back()->with('status', 'Dihapus.');
    }
}
