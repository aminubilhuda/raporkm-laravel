<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('dapodik_pd_id', 100)->nullable()->after('foto');
            $table->index('dapodik_pd_id');
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropIndex(['dapodik_pd_id']);
            $table->dropColumn('dapodik_pd_id');
        });
    }
};
