<?php

namespace Tests\Feature\Api\V1;

use App\Models\KelompokMapel;
use App\Models\Mapel;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapelApiTest extends TestCase
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

    public function test_index_returns_mapel_list(): void
    {
        $kk = KelompokMapel::create(['nama' => 'A']);
        Mapel::create(['kelompok_mapel_id' => $kk->id, 'kode' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75]);
        Mapel::create(['kelompok_mapel_id' => $kk->id, 'kode' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia', 'kkm' => 75]);

        $response = $this->getJson('/api/v1/tu/mapel', $this->authHeaders());

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [['id', 'kode', 'nama_mapel', 'kkm', 'kelompok_mapel']],
            ]);
    }

    public function test_index_search(): void
    {
        $kk = KelompokMapel::create(['nama' => 'A']);
        Mapel::create(['kelompok_mapel_id' => $kk->id, 'kode' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75]);
        Mapel::create(['kelompok_mapel_id' => $kk->id, 'kode' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia', 'kkm' => 75]);

        $response = $this->getJson('/api/v1/tu/mapel?search=MTK', $this->authHeaders());

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_filter_by_kelompok(): void
    {
        $kkA = KelompokMapel::create(['nama' => 'A']);
        $kkB = KelompokMapel::create(['nama' => 'B']);
        Mapel::create(['kelompok_mapel_id' => $kkA->id, 'kode' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75]);
        Mapel::create(['kelompok_mapel_id' => $kkB->id, 'kode' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia', 'kkm' => 75]);

        $response = $this->getJson('/api/v1/tu/mapel?kelompok_mapel_id='.$kkA->id, $this->authHeaders());

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_ordering_by_urutan(): void
    {
        $kk = KelompokMapel::create(['nama' => 'A']);
        Mapel::create(['kelompok_mapel_id' => $kk->id, 'kode' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75, 'urutan' => 2]);
        Mapel::create(['kelompok_mapel_id' => $kk->id, 'kode' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia', 'kkm' => 75, 'urutan' => 1]);

        $response = $this->getJson('/api/v1/tu/mapel', $this->authHeaders());

        $data = $response->json('data');
        $this->assertSame('Bahasa Indonesia', $data[0]['nama_mapel']);
        $this->assertSame('Matematika', $data[1]['nama_mapel']);
    }

    public function test_show_returns_mapel(): void
    {
        $kk = KelompokMapel::create(['nama' => 'A']);
        $mapel = Mapel::create(['kelompok_mapel_id' => $kk->id, 'kode' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75]);

        $response = $this->getJson('/api/v1/tu/mapel/'.$mapel->id, $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.kode', 'MTK')
            ->assertJsonPath('data.kelompok_mapel.nama', 'A');
    }

    public function test_show_404(): void
    {
        $response = $this->getJson('/api/v1/tu/mapel/9999', $this->authHeaders());

        $response->assertNotFound();
    }

    public function test_store_creates_mapel(): void
    {
        $kk = KelompokMapel::create(['nama' => 'A']);

        $response = $this->postJson('/api/v1/tu/mapel', [
            'kelompok_mapel_id' => $kk->id,
            'kode' => 'FIS',
            'nama_mapel' => 'Fisika',
            'kkm' => 70,
            'urutan' => 3,
        ], $this->authHeaders());

        $response->assertCreated()
            ->assertJsonPath('data.kode', 'FIS');

        $this->assertDatabaseHas('mapel', ['kode' => 'FIS']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/tu/mapel', [], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['kelompok_mapel_id', 'nama_mapel', 'kkm']);
    }

    public function test_store_validates_unique_kode(): void
    {
        $kk = KelompokMapel::create(['nama' => 'A']);
        Mapel::create(['kelompok_mapel_id' => $kk->id, 'kode' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75]);

        $response = $this->postJson('/api/v1/tu/mapel', [
            'kelompok_mapel_id' => $kk->id,
            'kode' => 'MTK',
            'nama_mapel' => 'Matematika 2',
            'kkm' => 75,
        ], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['kode']);
    }

    public function test_update_modifies_mapel(): void
    {
        $kk = KelompokMapel::create(['nama' => 'A']);
        $mapel = Mapel::create(['kelompok_mapel_id' => $kk->id, 'kode' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75]);

        $response = $this->putJson('/api/v1/tu/mapel/'.$mapel->id, [
            'nama_mapel' => 'Matematika Wajib',
            'kkm' => 80,
        ], $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.nama_mapel', 'Matematika Wajib')
            ->assertJsonPath('data.kkm', 80);
    }

    public function test_destroy_soft_deletes_mapel(): void
    {
        $kk = KelompokMapel::create(['nama' => 'A']);
        $mapel = Mapel::create(['kelompok_mapel_id' => $kk->id, 'kode' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75]);

        $response = $this->deleteJson('/api/v1/tu/mapel/'.$mapel->id, [], $this->authHeaders());

        $response->assertOk();
        $this->assertSoftDeleted('mapel', ['id' => $mapel->id]);
    }

    public function test_requires_tu_role(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);
        $token = $guru->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/tu/mapel');

        $response->assertForbidden();
    }
}
