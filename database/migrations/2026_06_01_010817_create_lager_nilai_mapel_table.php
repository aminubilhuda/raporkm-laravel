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
        Schema::create('lager_nilai_mapel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tahun_pelajaran_id');
            $table->unsignedBigInteger('semester_id');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('mapel_id');
            $table->unsignedBigInteger('siswa_id');
            $table->decimal('rata_formatif', 5, 2)->default(0);
            $table->decimal('rata_sumatif_ph', 5, 2)->default(0);
            $table->integer('sumatif_as')->default(0);
            $table->decimal('nilai_akhir', 5, 2)->default(0);
            $table->string('predikat', 30)->nullable();
            $table->text('deskripsi')->nullable();

            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajaran')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semester')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('mapel_id')->references('id')->on('mapel')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');

            $table->index(['tahun_pelajaran_id', 'semester_id', 'kelas_id', 'mapel_id'], 'lnmp_tp_smt_kls_mapel_idx');
            $table->index('siswa_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lager_nilai_mapel');
    }
};
