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
        Schema::create('proyek_tujuan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyek_kelas_id');
            $table->unsignedBigInteger('dimensi_id');
            $table->text('tujuan');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proyek_kelas_id')->references('id')->on('proyek_kelas');
            $table->foreign('dimensi_id')->references('id')->on('dimensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_tujuan');
    }
};
