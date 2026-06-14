<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Jobs\SyncDapodikJob;
use App\Models\DapodikSyncLog;
use App\Services\DapodikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DapodikController extends Controller
{
    public function __construct(private DapodikService $dapodik) {}

    private function extractIp(string $url): string
    {
        if (preg_match('#^https?://([^:/]+)#', $url, $m)) {
            return $m[1];
        }
        return $url;
    }

    public function index()
    {
        $rawUrl = DB::table('settings')->where('key', 'dapodik_url')->value('value') ?? '';
        $config = [
            'url' => $this->extractIp($rawUrl),
            'npsn' => DB::table('settings')->where('key', 'dapodik_npsn')->value('value') ?? '',
            'token' => DB::table('settings')->where('key', 'dapodik_token')->value('value') ?? '',
        ];

        $logs = DapodikSyncLog::latest()->take(10)->get();

        return view('tu.dapodik.index', compact('config', 'logs'));
    }

    public function updateConfig(Request $r)
    {
        $validated = $r->validate([
            'url' => 'required|string|max:255',
            'npsn' => 'required|string|max:20',
            'token' => 'required|string|max:255',
        ]);

        $url = trim($validated['url']);
        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $url = "http://{$url}:5774/WebService";
        }
        $validated['url'] = $url;

        foreach (['url' => 'dapodik_url', 'npsn' => 'dapodik_npsn', 'token' => 'dapodik_token'] as $input => $key) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $validated[$input], 'updated_at' => now()]
            );
        }

        return redirect()->route('tu.dapodik.index')->with('status', 'Konfigurasi Dapodik berhasil disimpan.');
    }

    public function sync(Request $r, string $endpoint)
    {
        $allowed = ['sekolah', 'peserta-didik', 'rombongan-belajar', 'pengguna', 'gtk', 'pembelajaran', 'all'];
        abort_unless(in_array($endpoint, $allowed), 404);

        if ($endpoint === 'all') {
            $endpoints = ['sekolah', 'gtk', 'pengguna', 'peserta-didik', 'rombongan-belajar', 'pembelajaran'];
            $jobs = array_map(fn ($ep) => new SyncDapodikJob($ep), $endpoints);

            $batch = Bus::batch($jobs)
                ->name('dapodik-sync-all')
                ->allowFailures()
                ->finally(function ($batch) {
                    $isSuccess = $batch->failedJobs === 0;

                    DapodikSyncLog::create([
                        'endpoint' => 'sync-all',
                        'status' => $isSuccess ? 'success' : 'error',
                        'records_count' => $batch->processedJobs(),
                        'message' => $isSuccess
                            ? 'Sinkronisasi semua data selesai.'
                            : $batch->failedJobs.' job(s) gagal dari '.$batch->totalJobs.' total.',
                        'batch_id' => $batch->id,
                        'progress_current' => $batch->processedJobs(),
                        'progress_total' => $batch->totalJobs,
                    ]);
                })
                ->dispatch();

            Cache::put('dapodik:active_batch_id', $batch->id, 600);

            DapodikSyncLog::create([
                'endpoint' => 'sync-all',
                'status' => 'batch_started',
                'records_count' => 0,
                'message' => 'Sinkronisasi semua data dimulai...',
                'batch_id' => $batch->id,
                'progress_current' => 0,
                'progress_total' => count($endpoints),
            ]);

            return redirect()->route('tu.dapodik.index')
                ->with('status', 'Sinkronisasi semua data dimulai di background. Progress dapat dilihat di bawah.');
        }

        SyncDapodikJob::dispatch($endpoint);

        return redirect()->route('tu.dapodik.index')
            ->with('status', "Sinkronisasi '{$endpoint}' dimulai di background. Cek log untuk hasil.");
    }

    public function log()
    {
        $logs = DapodikSyncLog::latest()->paginate(25);

        return view('tu.dapodik.log', compact('logs'));
    }

    public function status()
    {
        $batchId = Cache::get('dapodik:active_batch_id');
        $batch = $batchId ? Bus::findBatch($batchId) : null;

        $isRunning = $batch && ! $batch->finished();

        if ($batch && $batch->finished()) {
            Cache::forget('dapodik:active_batch_id');
        }

        $lastLog = DapodikSyncLog::where('endpoint', '!=', 'sync-all')
            ->latest()
            ->first();

        return response()->json([
            'running' => $isRunning,
            'batch' => $batch ? [
                'id' => $batch->id,
                'name' => $batch->name,
                'total' => $batch->totalJobs,
                'processed' => $batch->processedJobs(),
                'failed' => $batch->failedJobs,
                'pending' => $batch->pendingJobs,
                'progress' => $batch->progress(),
                'finished' => $batch->finished(),
                'cancelled' => $batch->cancelled(),
            ] : null,
            'last_log' => $lastLog,
            'recent_logs' => DapodikSyncLog::latest()->take(5)->get(),
        ]);
    }

    public function cancel()
    {
        $batchId = Cache::get('dapodik:active_batch_id');

        if (! $batchId) {
            return redirect()->route('tu.dapodik.index')
                ->with('error', 'Tidak ada sinkronisasi yang sedang berjalan.');
        }

        $batch = Bus::findBatch($batchId);

        if ($batch && ! $batch->finished()) {
            $batch->cancel();
            Cache::forget('dapodik:active_batch_id');

            return redirect()->route('tu.dapodik.index')
                ->with('status', 'Sinkronisasi berhasil dibatalkan.');
        }

        return redirect()->route('tu.dapodik.index')
            ->with('error', 'Batch tidak ditemukan atau sudah selesai.');
    }
}
