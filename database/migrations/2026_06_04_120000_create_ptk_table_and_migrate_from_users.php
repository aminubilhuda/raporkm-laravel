<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel ptk
        if (! Schema::hasTable('ptk')) {
            Schema::create('ptk', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('ptk_id', 100)->nullable()->index();
                $table->string('nuptk', 30)->nullable();
                $table->string('nik', 20)->nullable();
                $table->string('nip', 30)->nullable();
                $table->tinyInteger('kelamin')->nullable()->comment('1=Laki-laki, 2=Perempuan');
                $table->string('tempat_lahir', 100)->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->tinyInteger('agama')->nullable()->comment('FK ref_agama');
                $table->string('pendidikan_terakhir', 50)->nullable();
                $table->string('bidang_studi_terakhir', 100)->nullable();
                $table->string('pangkat_golongan', 50)->nullable();
                $table->string('status_kepegawaian', 50)->nullable();
                $table->string('jenis_ptk', 100)->nullable();
                $table->string('jabatan_ptk', 100)->nullable();
                $table->timestamps();
            });
        }

        // 2. Tambah ptk_id ke users (jika belum ada)
        if (! Schema::hasColumn('users', 'ptk_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('ptk_id')->nullable()->after('id')->constrained('ptk')->nullOnDelete();
            });
        }

        // 3. Pindahkan data GTK dari users ke ptk (jika kolom masih ada)
        if (Schema::hasColumn('users', 'nik')) {
            $users = DB::table('users')
                ->whereNotNull('nuptk')
                ->orWhereNotNull('nik')
                ->orWhereNotNull('ptk_id')
                ->get();

            foreach ($users as $user) {
                $ptkId = DB::table('ptk')->insertGetId([
                    'user_id' => $user->id,
                    'ptk_id' => $user->ptk_id,
                    'nuptk' => $user->nuptk,
                    'nik' => $user->nik,
                    'nip' => $user->nip,
                    'kelamin' => $user->kelamin,
                    'tempat_lahir' => $user->tempat_lahir,
                    'tanggal_lahir' => $user->tanggal_lahir,
                    'agama' => $user->agama,
                    'pendidikan_terakhir' => $user->pendidikan_terakhir,
                    'bidang_studi_terakhir' => $user->bidang_studi_terakhir,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('users')->where('id', $user->id)->update(['ptk_id' => $ptkId]);
            }
        }

        // 4. Drop kolom GTK dari users (jika masih ada)
        if (Schema::hasColumn('users', 'nik')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['ptk_id']);
                $table->dropColumn([
                    'nuptk', 'nik', 'nip', 'ptk_id', 'kelamin',
                    'tempat_lahir', 'tanggal_lahir', 'agama',
                    'pendidikan_terakhir', 'bidang_studi_terakhir',
                ]);
            });
        }
    }

    public function down(): void
    {
        // 1. Kembalikan kolom GTK ke users
        if (! Schema::hasColumn('users', 'nik')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nip', 30)->nullable()->after('jabatan');
                $table->string('nuptk', 30)->nullable()->after('nip');
                $table->string('ptk_id', 100)->nullable()->after('nuptk');
                $table->string('nik', 20)->nullable()->after('ptk_id');
                $table->tinyInteger('kelamin')->nullable()->after('nik')->comment('FK ref_jenis_kelamin');
                $table->string('tempat_lahir', 100)->nullable()->after('kelamin');
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
                $table->tinyInteger('agama')->nullable()->after('tanggal_lahir')->comment('FK ref_agama');
                $table->string('pendidikan_terakhir', 50)->nullable()->after('agama');
                $table->string('bidang_studi_terakhir', 100)->nullable()->after('pendidikan_terakhir');
            });
        }

        // 2. Kembalikan data dari ptk ke users (jika kolom sudah dikembalikan)
        if (Schema::hasColumn('users', 'nik')) {
            $ptkRecords = DB::table('ptk')->get();
            foreach ($ptkRecords as $ptk) {
                DB::table('users')->where('id', $ptk->user_id)->update([
                    'ptk_id' => $ptk->ptk_id,
                    'nuptk' => $ptk->nuptk,
                    'nik' => $ptk->nik,
                    'nip' => $ptk->nip,
                    'kelamin' => $ptk->kelamin,
                    'tempat_lahir' => $ptk->tempat_lahir,
                    'tanggal_lahir' => $ptk->tanggal_lahir,
                    'agama' => $ptk->agama,
                    'pendidikan_terakhir' => $ptk->pendidikan_terakhir,
                    'bidang_studi_terakhir' => $ptk->bidang_studi_terakhir,
                ]);
            }
        }

        // 3. Drop ptk_id dari users
        if (Schema::hasColumn('users', 'ptk_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['ptk_id']);
                $table->dropColumn('ptk_id');
            });
        }

        // 4. Drop tabel ptk
        Schema::dropIfExists('ptk');
    }
};
