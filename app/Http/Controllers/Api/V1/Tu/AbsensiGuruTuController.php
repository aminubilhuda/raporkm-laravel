<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Models\PresensiGuruTu;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AbsensiGuruTuController extends Controller
{
    public function rekap(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();

        $query = PresensiGuruTu::with(['user.ptk', 'tahunPelajaran', 'semester'])
            ->when($request->filled('tahun_pelajaran_id'), fn($q) => $q->where('tahun_pelajaran_id', $request->integer('tahun_pelajaran_id')))
            ->when($request->filled('semester_id'), fn($q) => $q->where('semester_id', $request->integer('semester_id')))
            ->when($request->filled('tanggal'), fn($q) => $q->where('tanggal', $request->date('tanggal')));

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        } else {
            $query->when($sekolah, fn($q) => $q->where('tahun_pelajaran_id', $sekolah->tahun_aktif));
        }

        $rekap = $query->orderBy('tanggal', 'desc')
            ->orderBy('user_id')
            ->paginate($request->integer('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => $rekap->items(),
            'meta' => [
                'current_page' => $rekap->currentPage(),
                'last_page' => $rekap->lastPage(),
                'per_page' => $rekap->perPage(),
                'total' => $rekap->total(),
            ],
        ]);
    }

    public function rekapHarian(Request $request): JsonResponse
    {
        $request->validate([
            'tanggal' => ['required', 'date'],
        ]);

        $tanggal = $request->date('tanggal');

        $data = PresensiGuruTu::with(['user.ptk'])
            ->where('tanggal', $tanggal)
            ->orderBy('user_id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function ringkasan(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();

        $totalGuru = \App\Models\User::whereIn('jabatan', [2, 3, 4])->count();

        $hadir = PresensiGuruTu::where('tanggal', now()->toDateString())
            ->whereNotNull('check_in')
            ->where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
            ->where('semester_id', $sekolah?->semester_aktif)
            ->count();

        $tepatWaktu = PresensiGuruTu::where('tanggal', now()->toDateString())
            ->where('status_check_in', 'tepat_waktu')
            ->where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
            ->where('semester_id', $sekolah?->semester_aktif)
            ->count();

        $terlambat = PresensiGuruTu::where('tanggal', now()->toDateString())
            ->where('status_check_in', 'terlambat')
            ->where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
            ->where('semester_id', $sekolah?->semester_aktif)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_guru_tu' => $totalGuru,
                'hadir' => $hadir,
                'tepat_waktu' => $tepatWaktu,
                'terlambat' => $terlambat,
                'belum_absen' => $totalGuru - $hadir,
                'tanggal' => now()->toDateString(),
            ],
        ]);
    }
}
