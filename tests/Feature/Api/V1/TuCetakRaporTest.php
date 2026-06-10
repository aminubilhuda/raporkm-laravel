<?php

namespace Tests\Feature\Api\V1;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TuCetakRaporTest extends TestCase
{
    use RefreshDatabase;

    private User $tuUser;

    protected function setUp(): void
    {
        parent::setUp();

        $tp = TahunPelajaran::create(['tahun' => '2025/2026', 'status' => 1]);
        $sem = Semester::create(['nama' => 'Ganjil', 'urutan' => 1, 'status' => 1]);
        Sekolah::create([
            'npsn' => '12345678',
            'nama_sekolah' => 'SMK Abdi Negara Tuban',
            'tahun_aktif' => $tp->id,
            'semester_aktif' => $sem->id,
        ]);

        $this->tuUser = User::factory()->create([
            'jabatan' => 2,
            'username' => 'tu01',
            'password' => bcrypt('password123'),
        ]);
    }

    private function authHeaders(): array
    {
        $token = $this->tuUser->createToken('test-device')->plainTextToken;

        return [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ];
    }

    public function test_index_returns_active_students(): void
    {
        $siswa = Siswa::factory()->create(['aktif' => 1]);
        $tp = TahunPelajaran::first();
        $sem = Semester::first();
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);
        SiswaKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
            'status' => 'aktif',
        ]);

        $response = $this->getJson('/api/v1/tu/cetak-rapor', $this->authHeaders());

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['siswa_id', 'nama_siswa', 'nisn', 'kelas_id', 'nama_kelas', 'tingkat']],
            ]);
    }

    public function test_cetak_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/tu/cetak-rapor', [], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['siswa_id', 'jenis']);
    }

    public function test_cetak_validates_jenis_values(): void
    {
        $response = $this->postJson('/api/v1/tu/cetak-rapor', [
            'siswa_id' => [1],
            'jenis' => 'invalid',
        ], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['jenis']);
    }

    public function test_requires_tu_role(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);
        $token = $guru->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/tu/cetak-rapor');

        $response->assertForbidden();
    }
}
