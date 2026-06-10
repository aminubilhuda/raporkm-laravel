<?php

use App\Models\DapodikSyncLog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Scheduled Tasks ──

// Cleanup old Dapodik sync logs (>30 days)
Schedule::call(function () {
    DapodikSyncLog::where('created_at', '<', now()->subDays(30))->delete();
})->dailyAt('02:00');

// Run database backup (via external script in cron)
// Backup is handled by cron: 0 3 * * * /www/wwwroot/km.smkabdinegara.sch.id/scripts/backup-db.sh
