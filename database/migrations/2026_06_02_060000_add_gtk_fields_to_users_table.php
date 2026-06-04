<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->after('nuptk');
            $table->string('tempat_lahir', 100)->nullable()->after('kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('pendidikan_terakhir', 50)->nullable()->after('tanggal_lahir');
            $table->string('bidang_studi_terakhir', 100)->nullable()->after('pendidikan_terakhir');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik', 'tempat_lahir', 'tanggal_lahir', 'pendidikan_terakhir', 'bidang_studi_terakhir']);
        });
    }
};
