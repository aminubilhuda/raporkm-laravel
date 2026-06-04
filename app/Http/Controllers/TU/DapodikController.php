<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\DapodikSyncLog;
use App\Services\DapodikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DapodikController extends Controller
{
    public function __construct(private DapodikService $dapodik) {}

    public function index()
    {
        $config = [
            'url' => DB::table('settings')->where('key', 'dapodik_url')->value('value') ?? '',
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

        try {
            $result = match ($endpoint) {
                'all' => $this->dapodik->syncAll(),
                'sekolah' => $this->dapodik->syncSekolahan(),
                'peserta-didik' => $this->dapodik->syncPesertaDidik(),
                'rombongan-belajar' => $this->dapodik->syncRombonganBelajar(),
                'pengguna' => $this->dapodik->syncPengguna(),
                'gtk' => $this->dapodik->syncGtk(),
                'pembelajaran' => $this->dapodik->syncPembelajaran(),
            };

            $status = ($result['failed'] ?? 0) > 0 ? 'error' : 'status';

            return redirect()->route('tu.dapodik.index')
                ->with($status, $result['message'])
                ->with('sync_result', $result);
        } catch (\Exception $e) {
            return redirect()->route('tu.dapodik.index')
                ->with('error', 'Gagal: '.$e->getMessage());
        }
    }

    public function log()
    {
        $logs = DapodikSyncLog::latest()->paginate(25);

        return view('tu.dapodik.log', compact('logs'));
    }
}
