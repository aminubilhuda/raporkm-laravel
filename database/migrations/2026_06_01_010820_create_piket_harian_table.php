<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('piket_harian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('hari_id');
            $table->unsignedBigInteger('tahun_pelajaran_id');
            $table->unsignedBigInteger('semester_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('hari_id')->references('id')->on('ref_hari');
            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajaran');
            $table->foreign('semester_id')->references('id')->on('semester');
            $table->unique(['user_id', 'hari_id', 'tahun_pelajaran_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piket_harian');
    }
};
