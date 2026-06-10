<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['endpoint', 'status', 'records_count', 'message', 'batch_id', 'progress_current', 'progress_total'])]
class DapodikSyncLog extends Model
{
    use HasFactory;

    protected $table = 'dapodik_sync_logs';
}
