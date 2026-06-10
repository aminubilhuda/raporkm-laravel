<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dapodik_sync_logs', function (Blueprint $table) {
            $table->string('batch_id')->nullable()->after('message')->index();
            $table->unsignedInteger('progress_current')->default(0)->after('batch_id');
            $table->unsignedInteger('progress_total')->default(0)->after('progress_current');
        });
    }

    public function down(): void
    {
        Schema::table('dapodik_sync_logs', function (Blueprint $table) {
            $table->dropIndex(['batch_id']);
            $table->dropColumn(['batch_id', 'progress_current', 'progress_total']);
        });
    }
};
