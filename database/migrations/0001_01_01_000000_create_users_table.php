<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('jabatan')->default(3)->comment('2=TU, 3=Guru, 4=Kepsek');
            $table->string('nama', 100);
            $table->string('nip', 30)->nullable();
            $table->string('nuptk', 30)->nullable();
            $table->string('username', 50)->unique();
            $table->string('email', 100)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('kelamin')->nullable()->comment('FK ref_jenis_kelamin');
            $table->tinyInteger('agama')->nullable()->comment('FK ref_agama');
            $table->string('kontak', 20)->nullable();
            $table->tinyInteger('id_kepegawaian')->nullable();
            $table->tinyInteger('ijazah')->nullable();
            $table->tinyInteger('id_tugas_tambahan')->nullable();
            $table->string('foto', 255)->nullable();
            $table->text('moto')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('jabatan');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
