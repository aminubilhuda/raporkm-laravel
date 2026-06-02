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
        Schema::create('pengingat', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->text('pesan');
            $table->unsignedTinyInteger('untuk_role')->comment('2=TU,3=Guru');
            $table->date('tanggal');
            $table->time('waktu')->nullable();
            $table->unsignedTinyInteger('dikirim')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengingat');
    }
};
