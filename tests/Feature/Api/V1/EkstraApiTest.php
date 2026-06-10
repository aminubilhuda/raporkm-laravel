<?php

namespace Tests\Feature\Api\V1;

use App\Models\Eskul;
use App\Models\PembinaEskul;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EkstraApiTest extends TestCase
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

    public function test_index_returns_ekstra_list(): void
    {
        Eskul::create(['nama_eskul' => 'Pramuka']);
        Eskul::create(['nama_eskul' => 'Basket']);

        $response = $this->getJson('/api/v1/tu/ekstra', $this->authHeaders());

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [['id', 'nama_eskul', 'keterangan', 'jumlah_siswa', 'pembina']],
            ]);
    }

    public function test_store_creates_ekstra(): void
    {
        $response = $this->postJson('/api/v1/tu/ekstra', [
            'nama_eskul' => 'Futsal',
        ], $this->authHeaders());

        $response->assertCreated()
            ->assertJsonPath('data.nama_eskul', 'Futsal');

        $this->assertDatabaseHas('eskul', ['nama_eskul' => 'Futsal']);
    }

    public function test_store_validates_required(): void
    {
        $response = $this->postJson('/api/v1/tu/ekstra', [], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['nama_eskul']);
    }

    public function test_update_modifies_ekstra(): void
    {
        $eskul = Eskul::create(['nama_eskul' => 'Old']);

        $response = $this->putJson('/api/v1/tu/ekstra/'.$eskul->id, [
            'nama_eskul' => 'New',
        ], $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.nama_eskul', 'New');
    }

    public function test_destroy_deletes_ekstra(): void
    {
        $eskul = Eskul::create(['nama_eskul' => 'To Delete']);

        $response = $this->deleteJson('/api/v1/tu/ekstra/'.$eskul->id, [], $this->authHeaders());

        $response->assertOk();
        $this->assertSoftDeleted('eskul', ['id' => $eskul->id]);
    }

    public function test_store_pembina(): void
    {
        $eskul = Eskul::create(['nama_eskul' => 'Pramuka']);
        $guru = User::factory()->create(['jabatan' => 3]);

        $response = $this->postJson('/api/v1/tu/ekstra/pembina', [
            'eskul_id' => $eskul->id,
            'user_id' => $guru->id,
        ], $this->authHeaders());

        $response->assertCreated();
        $this->assertDatabaseHas('pembina_eskul', [
            'eskul_id' => $eskul->id,
            'user_id' => $guru->id,
        ]);
    }

    public function test_destroy_pembina(): void
    {
        $eskul = Eskul::create(['nama_eskul' => 'Pramuka']);
        $guru = User::factory()->create(['jabatan' => 3]);
        $pembina = PembinaEskul::create([
            'eskul_id' => $eskul->id,
            'user_id' => $guru->id,
            'tahun_pelajaran_id' => Sekolah::first()->tahun_aktif,
        ]);

        $response = $this->deleteJson('/api/v1/tu/ekstra/pembina/'.$pembina->id, [], $this->authHeaders());

        $response->assertOk();
        $this->assertDatabaseMissing('pembina_eskul', ['id' => $pembina->id]);
    }

    public function test_requires_tu_role(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);
        $token = $guru->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/tu/ekstra');

        $response->assertForbidden();
    }
}
