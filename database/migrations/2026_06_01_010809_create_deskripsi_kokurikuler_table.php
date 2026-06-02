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
        Schema::create('deskripsi_kokurikuler', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dimensi_kokurikuler_id');
            $table->string('predikat', 20);
            $table->text('deskripsi');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dimensi_kokurikuler_id')->references('id')->on('dimensi_kokurikuler');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deskripsi_kokurikuler');
    }
};
