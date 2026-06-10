<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\PresensiGuruTu;
use App\Models\Sekolah;
use App\Services\GpsValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AbsensiGuruController extends Controller
{
    public function index(Request $request)
    {
        $sekolah = Sekolah::first();

        $presensi = PresensiGuruTu::with(['user.ptk'])
            ->where('user_id', $request->user()->id)
            ->where('tanggal', now()->toDateString())
            ->where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
            ->where('semester_id', $sekolah?->semester_actif)
            ->first();

        $riwayat = PresensiGuruTu::with(['user.ptk'])
            ->where('user_id', $request->user()->id)
            ->where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
            ->where('semester_id', $sekolah?->semester_aktif)
            ->orderBy('tanggal', 'desc')
            ->limit(30)
            ->get();

        return view('guru.absensi.index', compact('sekolah', 'presensi', 'riwayat'));
    }

    public function checkIn(Request $request)
    {
        $sekolah = Sekolah::first();

        if (!$sekolah || !$sekolah->latitude || !$sekolah->longitude) {
            return back()->withErrors(['gps' => 'Lokasi sekolah belum dikonfigurasi.']);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
            'foto_selfie_in' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $gps = new GpsValidationService();

        if (!$gps->isWithinRadius(
            $validated['latitude'],
            $validated['longitude'],
            $sekolah->latitude,
            $sekolah->longitude,
            $sekolah->radius_absen ?? 100
        )) {
            return back()->withErrors(['gps' => 'Anda berada di luar radius absensi sekolah.']);
        }

        $user = $request->user();

        DB::beginTransaction();
        try {
            $path = $request->file('foto_selfie_in')->store('absensi/guru', 'public');

            $jamMasuk = $sekolah->jam_masuk;
            $selisihMenit = now()->diffInMinutes($jamMasuk, false);
            $statusCheckIn = $selisihMenit <= 15 ? 'tepat_waktu' : 'terlambat';

            PresensiGuruTu::updateOrCreate(
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
                ]
            );

            DB::commit();

            return back()->with('status', 'Check-in berhasil.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['gps' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function checkOut(Request $request)
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
            return back()->withErrors(['gps' => 'Anda belum check-in hari ini.']);
        }

        if ($presensi->check_out) {
            return back()->withErrors(['gps' => 'Anda sudah check-out hari ini.']);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
            'foto_selfie_out' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $gps = new GpsValidationService();

        if (!$gps->isWithinRadius(
            $validated['latitude'],
            $validated['longitude'],
            $sekolah->latitude,
            $sekolah->longitude,
            $sekolah->radius_absen ?? 100
        )) {
            return back()->withErrors(['gps' => 'Anda berada di luar radius absensi sekolah.']);
        }

        DB::beginTransaction();
        try {
            $data = [
                'check_out' => now()->toTimeString(),
                'latitude_out' => $validated['latitude'],
                'longitude_out' => $validated['longitude'],
            ];

            if ($request->hasFile('foto_selfie_out')) {
                $data['foto_selfie_out'] = $request->file('foto_selfie_out')->store('absensi/guru', 'public');
            }

            if ($request->filled('keterangan')) {
                $data['keterangan'] = $validated['keterangan'];
            }

            $jamPulang = $sekolah->jam_pulang;
            $selisihMenit = now()->diffInMinutes($jamPulang, false);
            $data['status_check_out'] = $selisihMenit <= 5 ? 'pulang_tepat' : 'pulang_cepat';

            $presensi->update($data);

            DB::commit();

            return back()->with('status', 'Check-out berhasil.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['gps' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }
}
