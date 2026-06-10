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

class KelasApiTest extends TestCase
{
    use RefreshDatabase;

    private User $tuUser;

    private Tingkat $tingkat;

    private KompetensiKeahlian $kk;

    private TahunPelajaran $tp;

    private Semester $sem;

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
        $this->tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $this->kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);

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

    private function createKelas(string $nama = 'X TKJ 1'): Kelas
    {
        return Kelas::create([
            'nama_kelas' => $nama,
            'tingkat_id' => $this->tingkat->id,
            'kompetensi_keahlian_id' => $this->kk->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);
    }

    public function test_index_returns_kelas_list(): void
    {
        $this->createKelas('X TKJ 1');
        $this->createKelas('X TKJ 2');

        $response = $this->getJson('/api/v1/tu/kelas', $this->authHeaders());

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [['id', 'nama_kelas', 'tingkat', 'kompetensi_keahlian']],
                'meta',
            ]);
    }

    public function test_index_search(): void
    {
        $this->createKelas('X TKJ 1');
        $this->createKelas('XI RPL 1');

        $response = $this->getJson('/api/v1/tu/kelas?search=RPL', $this->authHeaders());

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_filter_by_tingkat(): void
    {
        $this->createKelas('X TKJ 1');
        $tingkat2 = Tingkat::create(['nama' => 'XI', 'angka' => 2, 'fase' => 'E', 'urutan' => 2]);
        Kelas::create([
            'nama_kelas' => 'XI TKJ 1',
            'tingkat_id' => $tingkat2->id,
            'kompetensi_keahlian_id' => $this->kk->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);

        $response = $this->getJson('/api/v1/tu/kelas?tingkat_id='.$tingkat2->id, $this->authHeaders());

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_show_returns_kelas(): void
    {
        $kelas = $this->createKelas();

        $response = $this->getJson('/api/v1/tu/kelas/'.$kelas->id, $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.nama_kelas', 'X TKJ 1')
            ->assertJsonStructure([
                'data' => ['tingkat' => ['nama'], 'kompetensi_keahlian' => ['nama']],
            ]);
    }

    public function test_show_404(): void
    {
        $response = $this->getJson('/api/v1/tu/kelas/9999', $this->authHeaders());

        $response->assertNotFound();
    }

    public function test_store_creates_kelas(): void
    {
        $response = $this->postJson('/api/v1/tu/kelas', [
            'nama_kelas' => 'X RPL 1',
            'tingkat_id' => $this->tingkat->id,
            'kompetensi_keahlian_id' => $this->kk->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ], $this->authHeaders());

        $response->assertCreated()
            ->assertJsonPath('data.nama_kelas', 'X RPL 1');

        $this->assertDatabaseHas('kelas', ['nama_kelas' => 'X RPL 1']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/tu/kelas', [], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['nama_kelas', 'tingkat_id', 'kompetensi_keahlian_id', 'tahun_pelajaran_id', 'semester_id']);
    }

    public function test_update_modifies_kelas(): void
    {
        $kelas = $this->createKelas();

        $response = $this->putJson('/api/v1/tu/kelas/'.$kelas->id, [
            'nama_kelas' => 'X TKJ 1 Updated',
        ], $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.nama_kelas', 'X TKJ 1 Updated');
    }

    public function test_destroy_deletes_kelas(): void
    {
        $kelas = $this->createKelas();

        $response = $this->deleteJson('/api/v1/tu/kelas/'.$kelas->id, [], $this->authHeaders());

        $response->assertOk();
        $this->assertDatabaseMissing('kelas', ['id' => $kelas->id]);
    }

    public function test_anggota_kelas_index(): void
    {
        $kelas = $this->createKelas();
        $siswa = Siswa::factory()->create(['aktif' => 1]);
        SiswaKelas::create([
            'siswa_id' => $siswa->id, 'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $this->tp->id, 'semester_id' => $this->sem->id, 'status' => 'aktif',
        ]);

        $response = $this->getJson('/api/v1/tu/anggota-kelas?kelas_id='.$kelas->id, $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.kelas_list.0.nama_kelas', 'X TKJ 1')
            ->assertJsonCount(1, 'data.anggota');
    }

    public function test_anggota_kelas_store(): void
    {
        $kelas = $this->createKelas();
        $siswa = Siswa::factory()->create(['aktif' => 1]);

        $response = $this->postJson('/api/v1/tu/anggota-kelas', [
            'kelas_id' => $kelas->id,
            'siswa_id' => $siswa->id,
        ], $this->authHeaders());

        $response->assertCreated();
        $this->assertDatabaseHas('siswa_kelas', [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'status' => 'aktif',
        ]);
    }

    public function test_anggota_kelas_store_duplicate(): void
    {
        $kelas = $this->createKelas();
        $siswa = Siswa::factory()->create(['aktif' => 1]);
        SiswaKelas::create([
            'siswa_id' => $siswa->id, 'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $this->tp->id, 'semester_id' => $this->sem->id, 'status' => 'aktif',
        ]);

        $response = $this->postJson('/api/v1/tu/anggota-kelas', [
            'kelas_id' => $kelas->id,
            'siswa_id' => $siswa->id,
        ], $this->authHeaders());

        $response->assertStatus(422);
    }

    public function test_anggota_kelas_destroy(): void
    {
        $kelas = $this->createKelas();
        $siswa = Siswa::factory()->create(['aktif' => 1]);
        $anggota = SiswaKelas::create([
            'siswa_id' => $siswa->id, 'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $this->tp->id, 'semester_id' => $this->sem->id, 'status' => 'aktif',
        ]);

        $response = $this->deleteJson('/api/v1/tu/anggota-kelas/'.$anggota->id, [], $this->authHeaders());

        $response->assertOk();
        $this->assertSoftDeleted('siswa_kelas', ['id' => $anggota->id]);
    }

    public function test_requires_tu_role(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);
        $token = $guru->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/tu/kelas');

        $response->assertForbidden();
    }
}
