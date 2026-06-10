<?php

use App\Http\Controllers\Api\PwaAuthController;
use App\Http\Controllers\Api\PwaPushController;
use App\Http\Controllers\Api\PwaSyncController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Guru\AbsensiGuruController;
use App\Http\Controllers\Api\V1\Guru\CatatanRaporController;
use App\Http\Controllers\Api\V1\Guru\CetakRaporController;
use App\Http\Controllers\Api\V1\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Api\V1\Guru\EkstraController as GuruEkstraController;
use App\Http\Controllers\Api\V1\Guru\KelasKuController;
use App\Http\Controllers\Api\V1\Guru\KokurikulerController as GuruKokurikulerController;
use App\Http\Controllers\Api\V1\Guru\NilaiPrakerinController;
use App\Http\Controllers\Api\V1\Guru\P5bkController as GuruP5bkController;
use App\Http\Controllers\Api\V1\Guru\PenilaianController;
use App\Http\Controllers\Api\V1\Guru\PresensiController as GuruPresensiController;
use App\Http\Controllers\Api\V1\Guru\TujuanPembelajaranController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ReferensiController;
use App\Http\Controllers\Api\V1\SekolahController;
use App\Http\Controllers\Api\V1\Tu\AbsensiGuruTuController;
use App\Http\Controllers\Api\V1\Tu\AnggotaKelasController;
use App\Http\Controllers\Api\V1\Tu\CetakRaporController as TuCetakRaporController;
use App\Http\Controllers\Api\V1\Tu\DashboardController as TuDashboardController;
use App\Http\Controllers\Api\V1\Tu\EkstraController;
use App\Http\Controllers\Api\V1\Tu\KelasController;
use App\Http\Controllers\Api\V1\Tu\KelasWaliController;
use App\Http\Controllers\Api\V1\Tu\KokurikulerController;
use App\Http\Controllers\Api\V1\Tu\MapelController;
use App\Http\Controllers\Api\V1\Tu\MapelKelasController;
use App\Http\Controllers\Api\V1\Tu\P5bkController;
use App\Http\Controllers\Api\V1\Tu\PegawaiController;
use App\Http\Controllers\Api\V1\Tu\PengaturanController;
use App\Http\Controllers\Api\V1\Tu\PrakerinController;
use App\Http\Controllers\Api\V1\Tu\RekapPresensiController;
use App\Http\Controllers\Api\V1\Tu\SiswaController;
use Illuminate\Support\Facades\Route;

// ── PWA Routes (existing) ──
Route::post('/pwa/login', [PwaAuthController::class, 'login']);

Route::middleware('pwa.auth')->group(function () {
    Route::get('/pwa/check', [PwaAuthController::class, 'check']);
    Route::post('/pwa/logout', [PwaAuthController::class, 'logout']);
    Route::post('/pwa/refresh', [PwaAuthController::class, 'refresh']);

    // Push Notification Routes
    Route::get('/pwa/vapid-key', [PwaPushController::class, 'vapidKey']);
    Route::post('/pwa/subscribe', [PwaPushController::class, 'subscribe']);
    Route::post('/pwa/unsubscribe', [PwaPushController::class, 'unsubscribe']);
    Route::post('/pwa/unsubscribe-all', [PwaPushController::class, 'unsubscribeAll']);
    Route::get('/pwa/push-status', [PwaPushController::class, 'status']);

    // Background Sync
    Route::post('/pwa/sync', [PwaSyncController::class, 'sync']);
});

// Push Send (TU only, web auth)
Route::middleware(['auth:sanctum', 'auth', 'role:2'])->prefix('tu')->name('tu.')->group(function () {
    Route::post('/pwa/push/send', [PwaPushController::class, 'send']);
});

// ── React Native API v1 ──
Route::prefix('v1')->group(function () {
    // Public routes (with login throttling)
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');
    Route::get('/sekolah/publik', [SekolahController::class, 'publik']);

    // Protected routes (rate limited)
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/fcm', [AuthController::class, 'registerFcm']);
        Route::delete('/auth/fcm', [AuthController::class, 'unregisterFcm']);

        // ── Profile ──
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'changePassword']);

        // ── Shared Routes ──
        Route::get('/sekolah', [SekolahController::class, 'profile']);

        // ── Referensi Routes (public to all authenticated users) ──
        Route::get('/referensi', [ReferensiController::class, 'index']);
        Route::get('/referensi/dimensi-elemen', [ReferensiController::class, 'dimensiWithElemens']);
        Route::get('/referensi/{slug}', [ReferensiController::class, 'show']);

        // ── TU Panel (role:2) ──
        Route::middleware('role:2')->prefix('tu')->group(function () {
            Route::get('/dashboard', TuDashboardController::class);

            // Pegawai
            Route::get('/pegawai', [PegawaiController::class, 'index']);
            Route::get('/pegawai/{id}', [PegawaiController::class, 'show']);
            Route::post('/pegawai', [PegawaiController::class, 'store']);
            Route::put('/pegawai/{id}', [PegawaiController::class, 'update']);
            Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy']);

            // Siswa
            Route::get('/siswa', [SiswaController::class, 'index']);
            Route::get('/siswa/{id}', [SiswaController::class, 'show']);
            Route::post('/siswa', [SiswaController::class, 'store']);
            Route::put('/siswa/{id}', [SiswaController::class, 'update']);
            Route::delete('/siswa/{id}', [SiswaController::class, 'destroy']);

            // Kelas
            Route::get('/kelas', [KelasController::class, 'index']);
            Route::get('/kelas/{id}', [KelasController::class, 'show']);
            Route::post('/kelas', [KelasController::class, 'store']);
            Route::put('/kelas/{id}', [KelasController::class, 'update']);
            Route::delete('/kelas/{id}', [KelasController::class, 'destroy']);

            // Anggota Kelas
            Route::get('/anggota-kelas', [AnggotaKelasController::class, 'index']);
            Route::post('/anggota-kelas', [AnggotaKelasController::class, 'store']);
            Route::delete('/anggota-kelas/{id}', [AnggotaKelasController::class, 'destroy']);

            // Mapel
            Route::get('/mapel', [MapelController::class, 'index']);
            Route::get('/mapel/{id}', [MapelController::class, 'show']);
            Route::post('/mapel', [MapelController::class, 'store']);
            Route::put('/mapel/{id}', [MapelController::class, 'update']);
            Route::delete('/mapel/{id}', [MapelController::class, 'destroy']);

            // Mapel Kelas (guru pengampu assignment)
            Route::get('/mapel-kelas', [MapelKelasController::class, 'index']);
            Route::post('/mapel-kelas', [MapelKelasController::class, 'store']);
            Route::put('/mapel-kelas/{id}', [MapelKelasController::class, 'update']);
            Route::delete('/mapel-kelas/{id}', [MapelKelasController::class, 'destroy']);
            Route::post('/mapel-kelas/batch', [MapelKelasController::class, 'batch']);

            // Kelas Wali
            Route::get('/kelas-wali', [KelasWaliController::class, 'index']);
            Route::post('/kelas-wali', [KelasWaliController::class, 'store']);
            Route::delete('/kelas-wali/{id}', [KelasWaliController::class, 'destroy']);

            // P5BK
            Route::get('/p5bk', [P5bkController::class, 'index']);
            Route::post('/p5bk', [P5bkController::class, 'store']);
            Route::put('/p5bk/{id}', [P5bkController::class, 'update']);
            Route::delete('/p5bk/{id}', [P5bkController::class, 'destroy']);
            Route::get('/p5bk/proyek-kelas', [P5bkController::class, 'proyek_kelas']);

            // P5BK Dimensi
            Route::get('/p5bk/dimensi', [P5bkController::class, 'dimensiIndex']);
            Route::post('/p5bk/dimensi', [P5bkController::class, 'dimensiStore']);
            Route::put('/p5bk/dimensi/{id}', [P5bkController::class, 'dimensiUpdate']);
            Route::delete('/p5bk/dimensi/{id}', [P5bkController::class, 'dimensiDestroy']);

            // P5BK Elemen
            Route::get('/p5bk/elemen', [P5bkController::class, 'elemenIndex']);
            Route::post('/p5bk/elemen', [P5bkController::class, 'elemenStore']);
            Route::put('/p5bk/elemen/{id}', [P5bkController::class, 'elemenUpdate']);
            Route::delete('/p5bk/elemen/{id}', [P5bkController::class, 'elemenDestroy']);

            // P5BK SubElemen
            Route::get('/p5bk/sub-elemen', [P5bkController::class, 'subElemenIndex']);
            Route::post('/p5bk/sub-elemen', [P5bkController::class, 'subElemenStore']);
            Route::put('/p5bk/sub-elemen/{id}', [P5bkController::class, 'subElemenUpdate']);
            Route::delete('/p5bk/sub-elemen/{id}', [P5bkController::class, 'subElemenDestroy']);

            // Kokurikuler
            Route::get('/kokurikuler', [KokurikulerController::class, 'index']);
            Route::post('/kokurikuler', [KokurikulerController::class, 'store']);
            Route::put('/kokurikuler/{id}', [KokurikulerController::class, 'update']);
            Route::delete('/kokurikuler/{id}', [KokurikulerController::class, 'destroy']);
            Route::post('/kokurikuler/deskripsi', [KokurikulerController::class, 'storeDeskripsi']);
            Route::delete('/kokurikuler/deskripsi/{id}', [KokurikulerController::class, 'destroyDeskripsi']);

            // Ekstra
            Route::get('/ekstra', [EkstraController::class, 'index']);
            Route::post('/ekstra', [EkstraController::class, 'store']);
            Route::put('/ekstra/{id}', [EkstraController::class, 'update']);
            Route::delete('/ekstra/{id}', [EkstraController::class, 'destroy']);
            Route::post('/ekstra/pembina', [EkstraController::class, 'storePembina']);
            Route::delete('/ekstra/pembina/{id}', [EkstraController::class, 'destroyPembina']);

            // Prakerin
            Route::get('/prakerin', [PrakerinController::class, 'index']);
            Route::post('/prakerin', [PrakerinController::class, 'store']);
            Route::put('/prakerin/{id}', [PrakerinController::class, 'update']);
            Route::delete('/prakerin/{id}', [PrakerinController::class, 'destroy']);
            Route::get('/prakerin/peserta', [PrakerinController::class, 'peserta']);
            Route::post('/prakerin/peserta', [PrakerinController::class, 'storePeserta']);
            Route::delete('/prakerin/peserta/{id}', [PrakerinController::class, 'destroyPeserta']);

            // Rekap Presensi
            Route::get('/rekap-presensi', [RekapPresensiController::class, 'index']);
            Route::get('/rekap-presensi/detail', [RekapPresensiController::class, 'detail']);

            // Absensi Guru & TU
            Route::get('/absensi/rekap', [AbsensiGuruTuController::class, 'rekap']);
            Route::get('/absensi/rekap-harian', [AbsensiGuruTuController::class, 'rekapHarian']);
            Route::get('/absensi/ringkasan', [AbsensiGuruTuController::class, 'ringkasan']);

            // Pengaturan
            Route::get('/pengaturan', [PengaturanController::class, 'index']);
            Route::put('/pengaturan', [PengaturanController::class, 'update']);
            Route::get('/pengaturan/tahun-pelajaran', [PengaturanController::class, 'tahunPelajaran']);
            Route::get('/pengaturan/semester', [PengaturanController::class, 'semester']);

            // Cetak Rapor (TU - admin)
            Route::get('/cetak-rapor', [TuCetakRaporController::class, 'index']);
            Route::post('/cetak-rapor', [TuCetakRaporController::class, 'cetak']);

            Route::post('/push/send', [PwaPushController::class, 'send']);
        });

        // ── Guru Panel (role:3,4) ──
        Route::middleware('role:3,4')->prefix('guru')->group(function () {
            Route::get('/dashboard', GuruDashboardController::class);

            // Kelas Ku
            Route::get('/kelas-ku', [KelasKuController::class, 'index']);
            Route::get('/kelas-ku/{id}/siswa', [KelasKuController::class, 'siswa']);

            // Penilaian
            Route::get('/penilaian', [PenilaianController::class, 'index']);
            Route::post('/penilaian/formatif', [PenilaianController::class, 'storeFormatif']);
            Route::post('/penilaian/sumatif-ph', [PenilaianController::class, 'storeSumatifPh']);
            Route::post('/penilaian/sumatif-as', [PenilaianController::class, 'storeSumatifAs']);
            Route::post('/penilaian/sumatif-ts', [PenilaianController::class, 'storeSumatifTs']);

            // Tujuan Pembelajaran
            Route::get('/tujuan-pembelajaran', [TujuanPembelajaranController::class, 'index']);
            Route::post('/tujuan-pembelajaran', [TujuanPembelajaranController::class, 'store']);
            Route::put('/tujuan-pembelajaran/{id}', [TujuanPembelajaranController::class, 'update']);
            Route::delete('/tujuan-pembelajaran/{id}', [TujuanPembelajaranController::class, 'destroy']);

            // Catatan Rapor
            Route::get('/catatan-rapor', [CatatanRaporController::class, 'index']);
            Route::post('/catatan-rapor', [CatatanRaporController::class, 'store']);

            // Presensi
            Route::get('/presensi', [GuruPresensiController::class, 'index']);
            Route::post('/presensi', [GuruPresensiController::class, 'store']);
            Route::get('/presensi/rekap', [GuruPresensiController::class, 'rekap']);

            // Absensi GPS
            Route::post('/absensi/check-in', [AbsensiGuruController::class, 'checkIn']);
            Route::post('/absensi/check-out', [AbsensiGuruController::class, 'checkOut']);
            Route::get('/absensi/status-hari-ini', [AbsensiGuruController::class, 'todayStatus']);
            Route::get('/absensi/riwayat', [AbsensiGuruController::class, 'riwayat']);

            // Kokurikuler
            Route::get('/kokurikuler', [GuruKokurikulerController::class, 'index']);
            Route::post('/kokurikuler', [GuruKokurikulerController::class, 'store']);

            // P5BK
            Route::get('/p5bk', [GuruP5bkController::class, 'index']);
            Route::get('/p5bk/penilaian', [GuruP5bkController::class, 'penilaian']);
            Route::post('/p5bk/penilaian', [GuruP5bkController::class, 'storePenilaian']);

            // Ekstra
            Route::get('/ekstra', [GuruEkstraController::class, 'index']);
            Route::get('/ekstra/{id}/siswa', [GuruEkstraController::class, 'siswa']);
            Route::post('/ekstra/penilaian', [GuruEkstraController::class, 'storePenilaian']);

            // Nilai Prakerin
            Route::get('/nilai-prakerin', [NilaiPrakerinController::class, 'index']);
            Route::post('/nilai-prakerin', [NilaiPrakerinController::class, 'store']);

            // Cetak Rapor
            Route::get('/cetak-rapor', [CetakRaporController::class, 'index']);
            Route::get('/cetak-rapor/{id}/siswa', [CetakRaporController::class, 'siswa']);
            Route::post('/cetak-rapor', [CetakRaporController::class, 'cetak']);
        });
    });
});

// ── Legacy Sanctum routes ──
Route::middleware('auth:sanctum')->post('/ping', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});
