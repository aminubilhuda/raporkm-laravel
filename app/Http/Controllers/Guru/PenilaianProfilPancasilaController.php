<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Dimensi;
use App\Models\Kelas;
use App\Models\NilaiAssesmenSubelemen;
use App\Models\ProyekKelas;
use App\Models\Sekolah;
use App\Models\SiswaKelas;

class PenilaianProfilPancasilaController extends Controller
{
    public function index(?Kelas $kelas = null)
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        $kelasWali = $user->kelasWali()
            ->when($taId, fn ($q) => $q->where('kelas_wali.tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('kelas_wali.semester_id', $semesterId))
            ->with('tingkat', 'kompetensiKeahlian')
            ->get();

        $authorized = $kelas && $kelasWali->contains('id', $kelas->id);

        $proyeks = collect();

        if ($authorized) {
            $proyeks = ProyekKelas::where('kelas_id', $kelas->id)
                ->where('user_id', $user->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->with('proyekTema')
                ->get();
        }

        return view('guru.penilaian-profil-pancasila.index', compact('kelasWali', 'kelas', 'authorized', 'proyeks'));
    }

    public function penilaian(ProyekKelas $proyekKelas)
    {
        $user = auth()->user();
        abort_unless($this->isWali($proyekKelas->kelas_id), 403);

        $sekolah = Sekolah::first();
        $taId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        $siswa = SiswaKelas::where('kelas_id', $proyekKelas->kelas_id)
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->with('siswa')
            ->get();

        $dimensiList = Dimensi::with('elemens.subElemens')->orderBy('urutan')->get();

        $nilaiSubelemen = NilaiAssesmenSubelemen::where('proyek_kelas_id', $proyekKelas->id)
            ->get()
            ->keyBy(fn ($n) => "{$n->siswa_id}_{$n->sub_elemen_id}");

        return view('guru.penilaian-profil-pancasila.penilaian', compact('proyekKelas', 'siswa', 'dimensiList', 'nilaiSubelemen'));
    }

    public function storePenilaian(ProyekKelas $proyekKelas)
    {
        abort_unless($this->isWali($proyekKelas->kelas_id), 403);

        $sekolah = Sekolah::first();
        $data = request()->validate([
            'siswa_id' => 'required|array',
            'siswa_id.*' => 'exists:siswa,id',
            'nilai' => 'array',
            'nilai.*.*' => 'nullable|integer|min:0|max:100',
        ]);

        foreach ($data['siswa_id'] as $siswaId) {
            foreach (($data['nilai'][$siswaId] ?? []) as $subElemenId => $nilai) {
                NilaiAssesmenSubelemen::updateOrCreate(
                    [
                        'proyek_kelas_id' => $proyekKelas->id,
                        'sub_elemen_id' => $subElemenId,
                        'siswa_id' => $siswaId,
                    ],
                    [
                        'nilai' => $nilai,
                        'tahun_pelajaran_id' => session('selected_tahun', $sekolah?->tahun_aktif),
                        'semester_id' => session('selected_semester', $sekolah?->semester_aktif),
                    ]
                );
            }
        }

        return redirect()->route('guru.penilaian-profil-pancasila.penilaian', $proyekKelas)
            ->with('status', 'Nilai Profil Pancasila berhasil disimpan.');
    }

    private function isWali(int $kelasId): bool
    {
        $sekolah = Sekolah::first();

        return auth()->user()->kelasWali()
            ->where('kelas_wali.tahun_pelajaran_id', session('selected_tahun', $sekolah?->tahun_aktif))
            ->where('kelas_wali.semester_id', session('selected_semester', $sekolah?->semester_aktif))
            ->where('kelas_id', $kelasId)->exists();
    }
}
