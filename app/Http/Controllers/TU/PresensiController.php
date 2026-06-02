<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Presensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function rekap(Request $r)
    {
        $kelasId = $r->get('kelas_id');
        $kelass = Kelas::orderBy('nama_kelas')->get();
        $rekap = collect();

        if ($kelasId) {
            $rekap = Presensi::selectRaw('siswa_id, jenis_absen_id, count(*) as total')
                ->with(['siswa', 'jenisAbsen'])
                ->where('kelas_id', $kelasId)
                ->groupBy('siswa_id', 'jenis_absen_id')
                ->get()
                ->groupBy('siswa_id');
        }

        return view('tu.presensi.rekap', compact('kelass', 'rekap', 'kelasId'));
    }
}
