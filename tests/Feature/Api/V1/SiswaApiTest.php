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

class SiswaApiTest extends TestCase
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

    public function test_index_returns_siswa_list(): void
    {
        Siswa::factory()->create(['nama_siswa' => 'Andi', 'aktif' => 1]);
        Siswa::factory()->create(['nama_siswa' => 'Budi', 'aktif' => 1]);

        $response = $this->getJson('/api/v1/tu/siswa', $this->authHeaders());

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [['id', 'nama_siswa', 'nisn', 'kelamin', 'aktif']],
                'meta',
            ]);
    }

    public function test_index_search_by_nama(): void
    {
        Siswa::factory()->create(['nama_siswa' => 'Andi Saputra', 'aktif' => 1]);
        Siswa::factory()->create(['nama_siswa' => 'Budi Hartono', 'aktif' => 1]);

        $response = $this->getJson('/api/v1/tu/siswa?search=Andi', $this->authHeaders());

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_filter_by_kelas(): void
    {
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $tp = TahunPelajaran::first();
        $sem = Semester::first();
        $kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);

        $siswa1 = Siswa::factory()->create(['aktif' => 1]);
        $siswa2 = Siswa::factory()->create(['aktif' => 1]);
        SiswaKelas::create([
            'siswa_id' => $siswa1->id, 'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tp->id, 'semester_id' => $sem->id, 'status' => 'aktif',
        ]);

        $response = $this->getJson('/api/v1/tu/siswa?kelas_id='.$kelas->id, $this->authHeaders());

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_show_returns_siswa(): void
    {
        $siswa = Siswa::factory()->create(['nama_siswa' => 'Andi']);

        $response = $this->getJson('/api/v1/tu/siswa/'.$siswa->id, $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.nama_siswa', 'Andi');
    }

    public function test_show_404(): void
    {
        $response = $this->getJson('/api/v1/tu/siswa/9999', $this->authHeaders());

        $response->assertNotFound();
    }

    public function test_store_creates_siswa(): void
    {
        $response = $this->postJson('/api/v1/tu/siswa', [
            'nama_siswa' => 'Siswa Baru',
            'nisn' => '0012345678',
            'nis' => '1234',
            'kelamin' => 1,
        ], $this->authHeaders());

        $response->assertCreated()
            ->assertJsonPath('data.nama_siswa', 'Siswa Baru');

        $this->assertDatabaseHas('siswa', ['nisn' => '0012345678', 'nis' => '1234']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/tu/siswa', [], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['nama_siswa', 'kelamin']);
    }

    public function test_update_modifies_siswa(): void
    {
        $siswa = Siswa::factory()->create(['nama_siswa' => 'Old Name']);

        $response = $this->putJson('/api/v1/tu/siswa/'.$siswa->id, [
            'nama_siswa' => 'New Name',
        ], $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.nama_siswa', 'New Name');
    }

    public function test_destroy_soft_deletes_siswa(): void
    {
        $siswa = Siswa::factory()->create(['aktif' => 1]);

        $response = $this->deleteJson('/api/v1/tu/siswa/'.$siswa->id, [], $this->authHeaders());

        $response->assertOk();
        $this->assertDatabaseHas('siswa', ['id' => $siswa->id, 'aktif' => 0]);
        $this->assertSoftDeleted('siswa', ['id' => $siswa->id]);
    }

    public function test_requires_tu_role(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);
        $token = $guru->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/tu/siswa');

        $response->assertForbidden();
    }
}
