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
        Schema::create('proyek_subelemen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyek_kelas_id');
            $table->unsignedBigInteger('sub_elemen_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proyek_kelas_id')->references('id')->on('proyek_kelas');
            $table->foreign('sub_elemen_id')->references('id')->on('sub_elemen');
            $table->unique(['proyek_kelas_id', 'sub_elemen_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_subelemen');
    }
};
