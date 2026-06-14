<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\Tingkat;
use Illuminate\Http\Request;

class AnggotaKelasController extends Controller
{
    public function index(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $search = $request->get('search');
        $tingkatId = $request->get('tingkat_id');
        $jurusanId = $request->get('jurusan_id');

        $kelass = Kelas::with('tingkat', 'kompetensiKeahlian')
            ->when($tingkatId, fn($q) => $q->where('tingkat_id', $tingkatId))
            ->when($jurusanId, fn($q) => $q->where('kompetensi_keahlian_id', $jurusanId))
            ->orderBy('nama_kelas')
            ->get();

        $tingkats = Tingkat::orderBy('urutan')->get();
        $jurusans = KompetensiKeahlian::orderBy('nama')->get();

        $anggota = collect();

        $sekolah = Sekolah::first();
        $enrolledIds = SiswaKelas::where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
            ->where('semester_id', $sekolah?->semester_aktif)
            ->whereNull('deleted_at')
            ->pluck('siswa_id');

        $availableSiswa = Siswa::where('aktif', 1)
            ->whereNotIn('id', $enrolledIds)
            ->orderBy('nama_siswa')
            ->get();

        if ($kelasId) {
            $kelas = Kelas::findOrFail($kelasId);
            $anggota = SiswaKelas::with('siswa')
                ->where('kelas_id', $kelasId)
                ->whereNull('deleted_at')
                ->when($search, fn($q) => $q->whereHas('siswa', fn($sq) => $sq->where('nama_siswa', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%")))
                ->get();
        }

        return view('tu.anggota-kelas.index', compact('kelass', 'anggota', 'kelasId', 'availableSiswa', 'search', 'tingkatId', 'jurusanId', 'tingkats', 'jurusans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'siswa_id' => ['required', 'exists:siswa,id'],
        ]);

        $sekolah = Sekolah::first();

        $exists = SiswaKelas::where('siswa_id', $validated['siswa_id'])
            ->where('tahun_pelajaran_id', $request->tahun_pelajaran_id ?? session('selected_tahun', $sekolah?->tahun_aktif))
            ->where('semester_id', $request->semester_id ?? session('selected_semester', $sekolah?->semester_aktif))
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Siswa sudah terdaftar di kelas lain pada tahun/semester ini.');
        }

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
