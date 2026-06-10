<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;

class AnggotaKelasController extends Controller
{
    public function index(Request $request)
    {
        $kelasId = $request->get('kelas_id');

        $kelass = Kelas::with('tingkat', 'kompetensiKeahlian')->orderBy('nama_kelas')->get();
        $anggota = collect();

        if ($kelasId) {
            $kelas = Kelas::findOrFail($kelasId);
            $anggota = SiswaKelas::with('siswa')
                ->where('kelas_id', $kelasId)
                ->whereNull('deleted_at')
                ->get();
        }

        return view('tu.anggota-kelas.index', compact('kelass', 'anggota', 'kelasId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'siswa_id' => ['required', 'exists:siswa,id'],
        ]);

        $exists = SiswaKelas::where('siswa_id', $validated['siswa_id'])
            ->where('kelas_id', $validated['kelas_id'])
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Siswa sudah terdaftar di kelas ini.');
        }

        $sekolah = Sekolah::first();

        $siswaKelas = SiswaKelas::create([
            'siswa_id' => $validated['siswa_id'],
            'kelas_id' => $validated['kelas_id'],
            'tahun_pelajaran_id' => $request->tahun_pelajaran_id ?? session('selected_tahun', $sekolah?->tahun_aktif),
            'semester_id' => $request->semester_id ?? session('selected_semester', $sekolah?->semester_aktif),
            'status' => 'aktif',
        ]);

        activity()
            ->performedOn($siswaKelas)
            ->event('created')
            ->withProperties(['nama' => $siswaKelas->siswa->nama_siswa ?? ''])
            ->log('Anggota kelas ditambahkan');

        return back()->with('status', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function destroy($id)
    {
        $siswaKelas = SiswaKelas::findOrFail($id);
        $nama = $siswaKelas->siswa->nama_siswa ?? '';
        $siswaKelas->delete();

        activity()
            ->performedOn($siswaKelas)
            ->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Anggota kelas dihapus');

        return back()->with('status', 'Siswa berhasil dikeluarkan dari kelas.');
    }
}
