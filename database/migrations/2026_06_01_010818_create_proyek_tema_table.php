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
        Schema::create('proyek_tema', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tema', 200);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('tahun_pelajaran_id');
            $table->unsignedBigInteger('semester_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajaran');
            $table->foreign('semester_id')->references('id')->on('semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_tema');
    }
};
