<?php

namespace Tests\Feature\Tu;

use App\Models\Kelas;
use App\Models\MutasiMasuk;
use App\Models\Organisasi;
use App\Models\PiketHarian;
use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TuWorkflowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_tu_workflow_lifecycle(): void
    {
        \DB::table('ref_hari')->insert([
            ['id' => 1, 'nama' => 'Senin', 'urutan' => 1],
            ['id' => 2, 'nama' => 'Selasa', 'urutan' => 2],
        ]);

        $tahun = TahunPelajaran::factory()->create();
        $semester = Semester::factory()->create();
        $tu = User::factory()->tataUsaha()->create();
        $kelas = Kelas::factory()->create();
        $siswa = Siswa::factory()->create();

        $this->actingAs($tu)->post(route('tu.anggota-kelas.store'), [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id,
            'semester_id' => $semester->id,
        ])->assertRedirect();
        $this->assertDatabaseHas('siswa_kelas', ['siswa_id' => $siswa->id, 'kelas_id' => $kelas->id]);

        $this->actingAs($tu)->post(route('tu.organisasi.store'), [
            'nama_organisasi' => 'OSIS',
            'keterangan' => 'Organisasi intra sekolah',
        ])->assertRedirect();
        $this->assertDatabaseHas('organisasi', ['nama_organisasi' => 'OSIS']);

        $this->actingAs($tu)->post(route('tu.piket-harian.store'), [
            'user_id' => $tu->id,
            'hari_id' => 1,
            'tahun_pelajaran_id' => $tahun->id,
            'semester_id' => $semester->id,
        ])->assertRedirect();
        $this->assertDatabaseHas('piket_harian', ['user_id' => $tu->id, 'hari_id' => 1]);

        $this->actingAs($tu)->post(route('tu.mutasi-masuk.store'), [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'asal_sekolah' => 'SMP Asal',
            'tanggal_masuk' => '2026-01-15',
            'tahun_pelajaran_id' => $tahun->id,
            'semester_id' => $semester->id,
        ])->assertRedirect();
        $this->assertDatabaseHas('mutasi_masuk', ['siswa_id' => $siswa->id, 'asal_sekolah' => 'SMP Asal']);
    }

    public function test_lulusan_unique_no_ijazah_validation(): void
    {
        $tahun = TahunPelajaran::factory()->create();
        $kelas = Kelas::factory()->create();
        $siswa = Siswa::factory()->create();
        $tu = User::factory()->tataUsaha()->create();

        \App\Models\Lulusan::factory()->create(['no_ijazah' => 'IJZ-UNIQUE-1']);

        $response = $this->actingAs($tu)->post(route('tu.lulusan.store'), [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id,
            'no_ijazah' => 'IJZ-UNIQUE-1',
        ]);

        $response->assertSessionHasErrors('no_ijazah');
    }

    public function test_lulusan_unique_no_ijazah_excludes_self_on_update(): void
    {
        $tahun = TahunPelajaran::factory()->create();
        $kelas = Kelas::factory()->create();
        $tu = User::factory()->tataUsaha()->create();
        $lulusan = \App\Models\Lulusan::factory()->create(['no_ijazah' => 'IJZ-UNIQUE-2']);

        $this->actingAs($tu)->put(route('tu.lulusan.update', $lulusan), [
            'no_ijazah' => 'IJZ-UNIQUE-2',
        ])->assertRedirect();

        $this->assertDatabaseHas('lulusan', ['id' => $lulusan->id, 'no_ijazah' => 'IJZ-UNIQUE-2']);
    }
}
