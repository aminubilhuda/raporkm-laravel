<?php

namespace App\Jobs;

use App\Models\DapodikSyncLog;
use App\Services\DapodikService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncDapodikJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public int $tries = 3;

    public function __construct(
        public string $endpoint
    ) {}

    public function handle(DapodikService $dapodik): void
    {
        $startTime = now();

        try {
            $result = match ($this->endpoint) {
                'sekolah' => $dapodik->syncSekolahan(),
                'peserta-didik' => $dapodik->syncPesertaDidik(),
                'rombongan-belajar' => $dapodik->syncRombonganBelajar(),
                'pengguna' => $dapodik->syncPengguna(),
                'gtk' => $dapodik->syncGtk(),
                'pembelajaran' => $dapodik->syncPembelajaran(),
                default => ['success' => 0, 'failed' => 0, 'message' => 'Endpoint tidak dikenal.'],
            };

            DapodikSyncLog::create([
                'endpoint' => $this->endpoint,
                'status' => ($result['failed'] ?? 0) > 0 ? 'error' : 'success',
                'records_count' => $result['success'] ?? 0,
                'message' => $result['message'] ?? 'Selesai',
                'batch_id' => $this->batch()?->id,
                'progress_current' => $result['success'] ?? 0,
                'progress_total' => ($result['success'] ?? 0) + ($result['failed'] ?? 0),
            ]);
        } catch (\Exception $e) {
            DapodikSyncLog::create([
                'endpoint' => $this->endpoint,
                'status' => 'error',
                'records_count' => 0,
                'message' => 'Exception: '.$e->getMessage(),
                'batch_id' => $this->batch()?->id,
                'progress_current' => 0,
                'progress_total' => 0,
            ]);

            $this->fail($e);
        }
    }

    public function uniqueId(): string
    {
        return "dapodik-sync-{$this->endpoint}";
    }

    public function displayName(): string
    {
        return "SyncDapodikJob: {$this->endpoint}";
    }

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }
}
