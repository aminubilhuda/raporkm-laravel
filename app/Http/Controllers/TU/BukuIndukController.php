<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BukuIndukController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with(['siswaKelas.kelas.tingkat', 'siswaKelas.kelas.kompetensiKeahlian'])
            ->withCount('siswaKelas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nama_siswa', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswaKelas', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id)
                    ->where('status', 'aktif');
            });
        }

        if ($request->filled('status')) {
            $query->where('aktif', $request->status === 'aktif' ? 1 : 0);
        } else {
            $query->where('aktif', 1);
        }

        $siswa = $query->orderBy('nama_siswa')->paginate(25)->withQueryString();
        $kelass = Kelas::with('tingkat', 'kompetensiKeahlian')->orderBy('nama_kelas')->get();

        return view('tu.buku-induk.index', compact('siswa', 'kelass'));
    }

    public function show(Siswa $bukuInduk)
    {
        $bukuInduk->load([
            'siswaKelas.kelas.tingkat',
            'siswaKelas.kelas.kompetensiKeahlian',
            'siswaKelas.tahunPelajaran',
            'siswaKelas.semester',
        ]);

        return response()->json($bukuInduk);
    }

    public function pdf(Request $request)
    {
        $query = Siswa::with(['siswaKelas.kelas.tingkat', 'siswaKelas.kelas.kompetensiKeahlian'])
            ->where('aktif', 1);

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswaKelas', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id)
                    ->where('status', 'aktif');
            });
        }

        $siswaList = $query->orderBy('nama_siswa')->get();
        $sekolah = Sekolah::first();

        $pdf = Pdf::loadView('tu.buku-induk.pdf', compact('siswaList', 'sekolah'))
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isFontManagerDisabled', true);

        $filename = 'buku-induk-siswa.pdf';

        return $pdf->download($filename);
    }
}
