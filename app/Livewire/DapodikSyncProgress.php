<?php

namespace App\Livewire;

use App\Models\DapodikSyncLog;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class DapodikSyncProgress extends Component
{
    public bool $isRunning = false;

    public ?string $batchId = null;

    public int $progress = 0;

    public int $total = 0;

    public int $processed = 0;

    public int $failed = 0;

    public int $pending = 0;

    public string $status = 'idle';

    public array $recentLogs = [];

    public function mount()
    {
        $this->checkStatus();
    }

    #[On('poll')]
    public function checkStatus()
    {
        $this->batchId = Cache::get('dapodik:active_batch_id');

        if ($this->batchId) {
            $batch = Bus::findBatch($this->batchId);

            if ($batch && ! $batch->finished()) {
                $this->isRunning = true;
                $this->total = $batch->totalJobs;
                $this->processed = $batch->processedJobs();
                $this->failed = $batch->failedJobs;
                $this->pending = $batch->pendingJobs;
                $this->progress = $batch->progress();
                $this->status = 'running';
            } else {
                $this->isRunning = false;
                $this->status = $batch?->cancelled() ? 'cancelled' : 'finished';
                Cache::forget('dapodik:active_batch_id');
            }
        } else {
            $this->isRunning = false;
            $this->status = 'idle';
        }

        $this->recentLogs = DapodikSyncLog::latest()
            ->take(5)
            ->get()
            ->map(fn ($log) => [
                'endpoint' => $log->endpoint,
                'status' => $log->status,
                'records_count' => $log->records_count,
                'message' => $log->message,
                'created_at' => $log->created_at->format('H:i:s'),
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dapodik-sync-progress');
    }
}
