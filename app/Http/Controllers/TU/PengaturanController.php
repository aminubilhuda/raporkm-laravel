<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\PembagianRaport;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $sekolah = Sekolah::first();
        $tahunPelajarans = TahunPelajaran::orderBy('tahun', 'desc')->get();
        $semesters = Semester::orderBy('urutan')->get();
        $pembagian = PembagianRaport::first();

        if (! $pembagian) {
            $pembagian = new PembagianRaport;
        }

        return view('tu.pengaturan.index', compact('sekolah', 'tahunPelajarans', 'semesters', 'pembagian'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'tahun_pelajaran_id' => ['required', 'exists:tahun_pelajaran,id'],
            'semester_id' => ['required', 'exists:semester,id'],
            'tanggal_mid' => ['nullable', 'date'],
            'tanggal_semester' => ['nullable', 'date'],
        ]);

        $sekolah = Sekolah::first();
        if ($sekolah) {
            $sekolah->update([
                'tahun_aktif' => $validated['tahun_pelajaran_id'],
                'semester_aktif' => $validated['semester_id'],
            ]);
        }

        PembagianRaport::updateOrCreate(
            ['tahun_pelajaran_id' => $validated['tahun_pelajaran_id'], 'semester_id' => $validated['semester_id']],
            [
                'tanggal_mid' => $validated['tanggal_mid'] ?? null,
                'tanggal_semester' => $validated['tanggal_semester'] ?? null,
            ]
        );

        return back()->with('status', 'Pengaturan berhasil disimpan.');
    }
}
