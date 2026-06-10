<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('lokasi');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedInteger('radius_absen')->default(100)->after('frame_peta');
            $table->time('jam_masuk')->default('07:00:00')->after('radius_absen');
            $table->time('jam_pulang')->default('15:00:00')->after('jam_masuk');
        });
    }

    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'radius_absen', 'jam_masuk', 'jam_pulang']);
        });
    }
};
