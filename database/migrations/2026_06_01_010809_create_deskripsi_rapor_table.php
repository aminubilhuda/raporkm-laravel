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
        Schema::create('deskripsi_rapor', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->integer('kktp')->default(0);
            $table->string('predikat', 50);
            $table->text('deskripsi');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deskripsi_rapor');
    }
};
