<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['endpoint', 'status', 'records_count', 'message'])]
class DapodikSyncLog extends Model
{
    protected $table = 'dapodik_sync_logs';
}
