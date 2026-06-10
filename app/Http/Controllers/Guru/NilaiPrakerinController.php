<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreNilaiPrakerinRequest;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\NilaiPrakerin;
use App\Models\Sekolah;
use App\Models\SiswaPrakerin;

class NilaiPrakerinController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        $daftarSiswa = SiswaPrakerin::where('user_id', $user->id)
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->with(['siswa', 'kelas', 'prakerin', 'nilaiPrakerin.mapel'])
            ->get();

        return view('guru.nilai-prakerin.index', compact('daftarSiswa'));
    }

    public function edit(SiswaPrakerin $siswaPrakerin)
    {
        abort_unless($siswaPrakerin->user_id === auth()->id(), 403);

        $siswaPrakerin->load(['siswa', 'kelas', 'prakerin', 'nilaiPrakerin.mapel']);

        // Get mapel from the student's kelas (mapelKelas relation)
        $mapelList = Mapel::whereHas('mapelKelas', function ($q) use ($siswaPrakerin) {
            $q->where('kelas_id', $siswaPrakerin->kelas_id);
        })->get();

        // Build existing nilai lookup
        $existingNilai = $siswaPrakerin->nilaiPrakerin
            ->keyBy('mapel_id');

        return view('guru.nilai-prakerin.edit', compact('siswaPrakerin', 'mapelList', 'existingNilai'));
    }

    public function store(StoreNilaiPrakerinRequest $request, SiswaPrakerin $siswaPrakerin)
    {
        abort_unless($siswaPrakerin->user_id === auth()->id(), 403);

        foreach ($request->mapel_id as $index => $mapelId) {
            $nilai = $request->nilai[$index] ?? 0;
            $deskripsi = $request->deskripsi[$index] ?? null;

            NilaiPrakerin::updateOrCreate(
                [
                    'siswa_prakerin_id' => $siswaPrakerin->id,
                    'mapel_id' => $mapelId,
                ],
                [
                    'nilai' => $nilai,
                    'deskripsi' => $deskripsi,
                ]
            );
        }

        return redirect()->route('guru.nilai-prakerin.index')
            ->with('status', 'Nilai prakerin berhasil disimpan.');
    }
}
