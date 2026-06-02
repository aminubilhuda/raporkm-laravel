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
        Schema::create('laporan_wa', function (Blueprint $table) {
            $table->id();
            $table->string('tujuan', 50);
            $table->text('pesan')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('respon')->nullable();
            $table->timestamp('dikirim_pada')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_wa');
    }
};
