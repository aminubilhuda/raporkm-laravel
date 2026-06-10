<?php

namespace Tests\Feature\Api\V1;

use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PegawaiApiTest extends TestCase
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

    public function test_index_returns_pegawai_list(): void
    {
        User::factory()->create(['jabatan' => 3, 'nama' => 'Budi Guru']);
        User::factory()->create(['jabatan' => 2, 'nama' => 'Sari TU']);

        $response = $this->getJson('/api/v1/tu/pegawai', $this->authHeaders());

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [['id', 'nama', 'jabatan', 'jabatan_label']],
                'meta',
            ]);
    }

    public function test_index_search(): void
    {
        User::factory()->create(['nama' => 'Budi Santoso', 'jabatan' => 3]);
        User::factory()->create(['nama' => 'Sari Dewi', 'jabatan' => 3]);

        $response = $this->getJson('/api/v1/tu/pegawai?search=Budi', $this->authHeaders());

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_filter_by_jabatan(): void
    {
        User::factory()->create(['jabatan' => 3]);
        User::factory()->create(['jabatan' => 3]);
        User::factory()->create(['jabatan' => 2]);

        $response = $this->getJson('/api/v1/tu/pegawai?jabatan=3', $this->authHeaders());

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_index_per_page_all(): void
    {
        User::factory()->count(5)->create(['jabatan' => 3]);

        $response = $this->getJson('/api/v1/tu/pegawai?per_page=all', $this->authHeaders());

        $response->assertOk();
        $this->assertCount(6, $response->json('data')); // 5 + 1 tuUser
        $this->assertArrayNotHasKey('meta', $response->json());
    }

    public function test_show_returns_pegawai(): void
    {
        $guru = User::factory()->create(['jabatan' => 3, 'nama' => 'Pak Ahmad']);

        $response = $this->getJson('/api/v1/tu/pegawai/'.$guru->id, $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.nama', 'Pak Ahmad')
            ->assertJsonPath('data.jabatan_label', 'Guru');
    }

    public function test_show_404(): void
    {
        $response = $this->getJson('/api/v1/tu/pegawai/9999', $this->authHeaders());

        $response->assertNotFound();
    }

    public function test_store_creates_pegawai(): void
    {
        $response = $this->postJson('/api/v1/tu/pegawai', [
            'nama' => 'Guru Baru',
            'username' => 'guru001',
            'password' => 'secret123',
            'jabatan' => 3,
            'nip' => '1234567890',
        ], $this->authHeaders());

        $response->assertCreated()
            ->assertJsonPath('data.nama', 'Guru Baru')
            ->assertJsonPath('data.username', 'guru001');

        $this->assertDatabaseHas('users', ['username' => 'guru001']);
        $this->assertDatabaseHas('ptk', ['nip' => '1234567890']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/tu/pegawai', [], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['nama', 'username', 'password', 'jabatan']);
    }

    public function test_store_validates_unique_username(): void
    {
        User::factory()->create(['username' => 'existing']);

        $response = $this->postJson('/api/v1/tu/pegawai', [
            'nama' => 'Test',
            'username' => 'existing',
            'password' => 'secret123',
            'jabatan' => 3,
        ], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['username']);
    }

    public function test_update_modifies_pegawai(): void
    {
        $guru = User::factory()->create(['jabatan' => 3, 'nama' => 'Old Name']);

        $response = $this->putJson('/api/v1/tu/pegawai/'.$guru->id, [
            'nama' => 'New Name',
        ], $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.nama', 'New Name');

        $this->assertDatabaseHas('users', ['id' => $guru->id, 'nama' => 'New Name']);
    }

    public function test_destroy_deletes_pegawai(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);

        $response = $this->deleteJson('/api/v1/tu/pegawai/'.$guru->id, [], $this->authHeaders());

        $response->assertOk();
        $this->assertSoftDeleted('users', ['id' => $guru->id]);
    }

    public function test_requires_tu_role(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);
        $token = $guru->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/tu/pegawai');

        $response->assertForbidden();
    }
}
