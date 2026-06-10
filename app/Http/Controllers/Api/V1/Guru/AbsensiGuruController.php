<?php

namespace App\Http\Controllers\Api\V1\Guru;

use App\Http\Controllers\Controller;
use App\Models\PresensiGuruTu;
use App\Models\Sekolah;
use App\Services\GpsValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AbsensiGuruController extends Controller
{
    public function checkIn(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();

        if (!$sekolah || !$sekolah->latitude || !$sekolah->longitude) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi sekolah belum dikonfigurasi.',
            ], 400);
        }

        $validated = $request->validate([
            'foto_selfie_in' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['required', 'numeric', 'min:-180', 'max:180'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $gps = new GpsValidationService();

        if (!$gps->isWithinRadius(
            $validated['latitude'],
            $validated['longitude'],
            $sekolah->latitude,
            $sekolah->longitude,
            $sekolah->radius_absen ?? 100,
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius absensi sekolah.',
            ], 403);
        }

        $user = $request->user();

        DB::beginTransaction();
        try {
            $path = $request->file('foto_selfie_in')
                ->store('absensi/guru', 'public');

            $jamMasuk = $sekolah->jam_masuk;
            $selisihMenit = now()->diffInMinutes($jamMasuk, false);

            if ($selisihMenit <= 15) {
                $statusCheckIn = 'tepat_waktu';
            } elseif ($selisihMenit > 15) {
                $statusCheckIn = 'terlambat';
            } else {
                $statusCheckIn = 'ditunda';
            }

            $presensi = PresensiGuruTu::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'tanggal' => now()->toDateString(),
                    'tahun_pelajaran_id' => $sekolah->tahun_aktif,
                    'semester_id' => $sekolah->semester_aktif,
                ],
                [
                    'check_in' => now()->toTimeString(),
                    'latitude_in' => $validated['latitude'],
                    'longitude_in' => $validated['longitude'],
                    'foto_selfie_in' => $path,
                    'status_check_in' => $statusCheckIn,
                    'keterangan' => $validated['keterangan'] ?? null,
                ],
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil.',
                'data' => $presensi,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan check-in: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function checkOut(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();
        $user = $request->user();

        $presensi = PresensiGuruTu::where([
            'user_id' => $user->id,
            'tanggal' => now()->toDateString(),
            'tahun_pelajaran_id' => $sekolah?->tahun_aktif,
            'semester_id' => $sekolah?->semester_aktif,
        ])->first();

        if (!$presensi || !$presensi->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check-in hari ini.',
            ], 400);
        }

        if ($presensi->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-out hari ini.',
            ], 400);
        }

        $validated = $request->validate([
            'foto_selfie_out' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['required', 'numeric', 'min:-180', 'max:180'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $gps = new GpsValidationService();

        if (!$gps->isWithinRadius(
            $validated['latitude'],
            $validated['longitude'],
            $sekolah->latitude,
            $sekolah->longitude,
            $sekolah->radius_absen ?? 100,
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius absensi sekolah.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            $data = [
                'check_out' => now()->toTimeString(),
                'latitude_out' => $validated['latitude'],
                'longitude_out' => $validated['longitude'],
            ];

            if ($request->hasFile('foto_selfie_out')) {
                $data['foto_selfie_out'] = $request->file('foto_selfie_out')
                    ->store('absensi/guru', 'public');
            }

            if ($request->filled('keterangan')) {
                $data['keterangan'] = $validated['keterangan'];
            }

            $jamPulang = $sekolah->jam_pulang;
            $selisihMenit = now()->diffInMinutes($jamPulang, false);

            $data['status_check_out'] = $selisihMenit <= 5 ? 'pulang_tepat' : 'pulang_cepat';

            $presensi->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil.',
                'data' => $presensi->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan check-out: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function todayStatus(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();
        $user = $request->user();

        $presensi = PresensiGuruTu::with(['user.ptk', 'tahunPelajaran', 'semester'])
            ->where('user_id', $user->id)
            ->where('tanggal', now()->toDateString())
            ->where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
            ->where('semester_id', $sekolah?->semester_aktif)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $presensi,
        ]);
    }

    public function riwayat(Request $request): JsonResponse
    {
        $sekolah = Sekolah::first();
        $user = $request->user();

        $perPage = $request->integer('per_page', 30);

        $riwayat = PresensiGuruTu::with(['user.ptk', 'tahunPelajaran', 'semester'])
            ->where('user_id', $user->id)
            ->where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
            ->where('semester_id', $sekolah?->semester_aktif)
            ->orderBy('tanggal', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $riwayat->items(),
            'meta' => [
                'current_page' => $riwayat->currentPage(),
                'last_page' => $riwayat->lastPage(),
                'per_page' => $riwayat->perPage(),
                'total' => $riwayat->total(),
            ],
        ]);
    }
}
