<?php

namespace Tests\Feature\Api\V1;

use App\Models\Kelas;
use App\Models\KelasWali;
use App\Models\KompetensiKeahlian;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KelasWaliApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Kelas $kelas;

    protected TahunPelajaran $tp;

    protected Semester $sem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tp = TahunPelajaran::create(['tahun' => '2025/2026', 'status' => 1]);
        $this->sem = Semester::create(['nama' => 'Ganjil', 'urutan' => 1, 'status' => 1]);
        Sekolah::create([
            'npsn' => '12345678',
            'nama_sekolah' => 'SMK Abdi Negara Tuban',
            'tahun_aktif' => $this->tp->id,
            'semester_aktif' => $this->sem->id,
        ]);

        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $this->kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);

        $this->user = User::factory()->tataUsaha()->create();

        Sanctum::actingAs($this->user);
    }

    public function test_index(): void
    {
        $response = $this->getJson('/api/v1/tu/kelas-wali');

        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_store(): void
    {
        $response = $this->postJson('/api/v1/tu/kelas-wali', [
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->user->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);

        $response->assertCreated()->assertJsonPath('success', true);
    }

    public function test_store_validation(): void
    {
        $response = $this->postJson('/api/v1/tu/kelas-wali', []);

        $response->assertUnprocessable()->assertJsonValidationErrors(['kelas_id', 'user_id']);
    }

    public function test_destroy(): void
    {
        $kw = KelasWali::create([
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->user->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);

        $response = $this->deleteJson('/api/v1/tu/kelas-wali/'.$kw->id);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseMissing('kelas_wali', ['id' => $kw->id]);
    }
}
