<?php

namespace Tests\Feature\Api\V1;

use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SekolahApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $tp = TahunPelajaran::create(['tahun' => '2025/2026', 'status' => 1]);
        $sem = Semester::create(['nama' => 'Ganjil', 'urutan' => 1, 'status' => 1]);
        Sekolah::create([
            'npsn' => '12345678',
            'nama_sekolah' => 'SMK Abdi Negara Tuban',
            'alamat' => 'Jl. Raya Tuban No. 1',
            'tahun_aktif' => $tp->id,
            'semester_aktif' => $sem->id,
        ]);

        $this->user = User::factory()->create([
            'jabatan' => 2,
            'username' => 'tu01',
            'password' => bcrypt('password123'),
        ]);
    }

    private function authHeaders(): array
    {
        $token = $this->user->createToken('test-device')->plainTextToken;

        return [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ];
    }

    public function test_sekolah_profile_returns_data(): void
    {
        $response = $this->getJson('/api/v1/sekolah', $this->authHeaders());

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'npsn' => '12345678',
                    'nama_sekolah' => 'SMK Abdi Negara Tuban',
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'npsn',
                    'nama_sekolah',
                    'alamat',
                    'tahun_aktif',
                    'semester_aktif',
                ],
            ]);
    }

    public function test_sekolah_profile_includes_related_data(): void
    {
        $response = $this->getJson('/api/v1/sekolah', $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.tahun_aktif.tahun', '2025/2026')
            ->assertJsonPath('data.semester_aktif.nama', 'Ganjil');
    }

    public function test_sekolah_profile_requires_auth(): void
    {
        $response = $this->getJson('/api/v1/sekolah');

        $response->assertUnauthorized();
    }
}
