<?php

namespace Tests\Feature\Api\V1;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengaturanApiTest extends TestCase
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

    public function test_index_returns_pengaturan(): void
    {
        $response = $this->getJson('/api/v1/tu/pengaturan', $this->authHeaders());

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'sekolah' => ['id', 'nama_sekolah', 'npsn'],
                    'tahun_aktif' => ['id', 'tahun'],
                    'semester_aktif' => ['id', 'nama'],
                ],
            ]);
    }

    public function test_update_changes_semester(): void
    {
        $newSem = Semester::create(['nama' => 'Genap', 'urutan' => 2, 'status' => 0]);

        $response = $this->putJson('/api/v1/tu/pengaturan', [
            'semester_aktif' => $newSem->id,
        ], $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.semester_aktif.id', $newSem->id);

        $this->assertDatabaseHas('sekolah', ['semester_aktif' => $newSem->id]);
    }

    public function test_tahun_pelajaran_list(): void
    {
        $tp = TahunPelajaran::first();
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => Sekolah::first()->semester_aktif,
        ]);

        $response = $this->getJson('/api/v1/tu/pengaturan/tahun-pelajaran', $this->authHeaders());

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'tahun', 'status', 'jumlah_kelas']],
            ]);
    }

    public function test_semester_list(): void
    {
        $response = $this->getJson('/api/v1/tu/pengaturan/semester', $this->authHeaders());

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'nama', 'urutan', 'status']],
            ]);
    }

    public function test_requires_tu_role(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);
        $token = $guru->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/tu/pengaturan');

        $response->assertForbidden();
    }
}
