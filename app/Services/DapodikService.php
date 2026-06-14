<?php

namespace App\Services;

use App\Services\Dapodik\DapodikClient;
use App\Services\Dapodik\GtkSyncService;
use App\Services\Dapodik\PembelajaranSyncService;
use App\Services\Dapodik\PenggunaSyncService;
use App\Services\Dapodik\RombelSyncService;
use App\Services\Dapodik\SekolahSyncService;
use App\Services\Dapodik\SiswaSyncService;

/**
 * Orchestrator for Dapodik sync operations.
 *
 * Delegates to focused services in App\Services\Dapodik\.
 * Kept for backward compatibility — existing callers can continue
 * to use this class without changes.
 */
class DapodikService
{
    private DapodikClient $client;

    private SekolahSyncService $sekolahSync;

    private GtkSyncService $gtkSync;

    private PenggunaSyncService $penggunaSync;

    private SiswaSyncService $siswaSync;

    private RombelSyncService $rombelSync;

    private PembelajaranSyncService $pembelajaranSync;

    public function __construct(?DapodikClient $client = null, ?SekolahService $sekolahService = null)
    {
        $this->client = $client ?? new DapodikClient;
        $sekolahService = $sekolahService ?? new SekolahService;
        $this->sekolahSync = new SekolahSyncService($this->client);
        $this->gtkSync = new GtkSyncService($this->client);
        $this->penggunaSync = new PenggunaSyncService($this->client);
        $this->siswaSync = new SiswaSyncService($this->client);
        $this->rombelSync = new RombelSyncService($this->client, $sekolahService);
        $this->pembelajaranSync = new PembelajaranSyncService($this->client, $sekolahService);
    }

    public function syncSekolahan(): array
    {
        return $this->sekolahSync->sync();
    }

    public function syncGtk(): array
    {
        return $this->gtkSync->sync();
    }

    public function syncPengguna(): array
    {
        return $this->penggunaSync->sync();
    }

    public function syncPesertaDidik(): array
    {
        return $this->siswaSync->sync();
    }

    public function syncRombonganBelajar(): array
    {
        return $this->rombelSync->sync();
    }

    public function syncPembelajaran(): array
    {
        return $this->pembelajaranSync->sync();
    }

    public function syncAll(): array
    {
        set_time_limit(0);

        $results = [
            'Sekolah' => $this->syncSekolahan(),
            'GTK' => $this->syncGtk(),
            'Pengguna' => $this->syncPengguna(),
            'Peserta Didik' => $this->syncPesertaDidik(),
            'Rombongan Belajar' => $this->syncRombonganBelajar(),
            'Pembelajaran' => $this->syncPembelajaran(),
        ];

        $totalSuccess = 0;
        $totalFailed = 0;
        $lines = [];

        foreach ($results as $label => $r) {
            $totalSuccess += $r['success'] ?? 0;
            $totalFailed += $r['failed'] ?? 0;
            $lines[] = "{$label}: {$r['success']} berhasil";
            if (($r['failed'] ?? 0) > 0) {
                $lines[count($lines) - 1] .= ", {$r['failed']} gagal";
            }
        }

        $msg = implode(' | ', $lines);

        return ['success' => $totalSuccess, 'failed' => $totalFailed, 'message' => $msg];
    }
}
