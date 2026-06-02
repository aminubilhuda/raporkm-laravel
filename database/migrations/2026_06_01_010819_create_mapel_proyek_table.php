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
        Schema::create('mapel_proyek', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyek_kelas_id');
            $table->unsignedBigInteger('mapel_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proyek_kelas_id')->references('id')->on('proyek_kelas');
            $table->foreign('mapel_id')->references('id')->on('mapel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapel_proyek');
    }
};
