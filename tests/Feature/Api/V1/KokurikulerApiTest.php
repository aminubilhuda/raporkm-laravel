<?php

namespace Tests\Feature\Api\V1;

use App\Models\DeskripsiKokurikuler;
use App\Models\DimensiKokurikuler;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KokurikulerApiTest extends TestCase
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

    public function test_index_returns_dimensi_list(): void
    {
        $dim = DimensiKokurikuler::create(['nama' => 'Religius']);
        DeskripsiKokurikuler::create([
            'dimensi_kokurikuler_id' => $dim->id,
            'predikat' => 'Baik',
            'deskripsi' => 'Siswa religius',
        ]);

        $response = $this->getJson('/api/v1/tu/kokurikuler', $this->authHeaders());

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [['id', 'nama', 'keterangan', 'deskripsi']],
            ]);
    }

    public function test_store_creates_dimensi(): void
    {
        $response = $this->postJson('/api/v1/tu/kokurikuler', [
            'nama' => 'Nasionalis',
        ], $this->authHeaders());

        $response->assertCreated()
            ->assertJsonPath('data.nama', 'Nasionalis');

        $this->assertDatabaseHas('dimensi_kokurikuler', ['nama' => 'Nasionalis']);
    }

    public function test_store_validates_required(): void
    {
        $response = $this->postJson('/api/v1/tu/kokurikuler', [], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['nama']);
    }

    public function test_update_modifies_dimensi(): void
    {
        $dim = DimensiKokurikuler::create(['nama' => 'Old']);

        $response = $this->putJson('/api/v1/tu/kokurikuler/'.$dim->id, [
            'nama' => 'New',
        ], $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.nama', 'New');
    }

    public function test_destroy_deletes_dimensi(): void
    {
        $dim = DimensiKokurikuler::create(['nama' => 'To Delete']);

        $response = $this->deleteJson('/api/v1/tu/kokurikuler/'.$dim->id, [], $this->authHeaders());

        $response->assertOk();
        $this->assertSoftDeleted('dimensi_kokurikuler', ['id' => $dim->id]);
    }

    public function test_store_deskripsi(): void
    {
        $dim = DimensiKokurikuler::create(['nama' => 'Religius']);

        $response = $this->postJson('/api/v1/tu/kokurikuler/deskripsi', [
            'dimensi_kokurikuler_id' => $dim->id,
            'predikat' => 'Sangat Baik',
            'deskripsi' => 'Siswa sangat religius',
        ], $this->authHeaders());

        $response->assertCreated();
        $this->assertDatabaseHas('deskripsi_kokurikuler', [
            'dimensi_kokurikuler_id' => $dim->id,
            'predikat' => 'Sangat Baik',
        ]);
    }

    public function test_destroy_deskripsi(): void
    {
        $dim = DimensiKokurikuler::create(['nama' => 'Religius']);
        $desk = DeskripsiKokurikuler::create([
            'dimensi_kokurikuler_id' => $dim->id,
            'predikat' => 'Baik',
            'deskripsi' => 'Baik',
        ]);

        $response = $this->deleteJson('/api/v1/tu/kokurikuler/deskripsi/'.$desk->id, [], $this->authHeaders());

        $response->assertOk();
        $this->assertSoftDeleted('deskripsi_kokurikuler', ['id' => $desk->id]);
    }

    public function test_requires_tu_role(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);
        $token = $guru->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/tu/kokurikuler');

        $response->assertForbidden();
    }
}
