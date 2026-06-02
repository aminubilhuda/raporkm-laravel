<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\SiswaKelas;
use App\Models\Tingkat;
use Illuminate\Http\Request;

class NaikKelasController extends Controller
{
    public function index()
    {
        $tingkats = Tingkat::orderBy('urutan')->get();
        $kelass = Kelas::with(['tingkat', 'kompetensiKeahlian'])->orderBy('nama_kelas')->get();

        return view('tu.naik-kelas.index', compact('tingkats', 'kelass'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'dari_kelas_id' => ['required', 'exists:kelas,id'],
            'ke_kelas_id' => ['required', 'exists:kelas,id', 'different:dari_kelas_id'],
            'siswa_ids' => ['required', 'array'],
            'siswa_ids.*' => ['exists:siswa_kelas,id'],
        ]);

        $count = 0;
        foreach ($validated['siswa_ids'] as $id) {
            $siswaKelas = SiswaKelas::find($id);
            if ($siswaKelas) {
                $siswaKelas->update(['status' => 'naik']);
                SiswaKelas::create([
                    'siswa_id' => $siswaKelas->siswa_id,
                    'kelas_id' => $validated['ke_kelas_id'],
                    'tahun_pelajaran_id' => $siswaKelas->tahun_pelajaran_id ?? 2,
                    'semester_id' => $siswaKelas->semester_id ?? 2,
                    'status' => 'aktif',
                ]);
                $count++;
            }
        }

        return back()->with('status', "{$count} siswa berhasil dinaikkan ke kelas baru.");
    }

    public function getSiswa(Kelas $kelas)
    {
        $siswa = SiswaKelas::with('siswa')
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->whereNull('deleted_at')
            ->get();

        return response()->json($siswa);
    }
}
