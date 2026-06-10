<?php

namespace Tests\Feature\Api\V1;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Prakerin;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaPrakerin;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrakerinApiTest extends TestCase
{
    use RefreshDatabase;

    private User $tuUser;

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

    public function test_index_returns_prakerin_list(): void
    {
        Prakerin::create([
            'nama_perusahaan' => 'PT Maju Jaya',
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);

        $response = $this->getJson('/api/v1/tu/prakerin', $this->authHeaders());

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [['id', 'nama_perusahaan', 'alamat', 'PIC', 'jumlah_siswa']],
            ]);
    }

    public function test_index_search(): void
    {
        Prakerin::create([
            'nama_perusahaan' => 'PT Maju Jaya',
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);
        Prakerin::create([
            'nama_perusahaan' => 'CV Berkah',
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);

        $response = $this->getJson('/api/v1/tu/prakerin?search=Maju', $this->authHeaders());

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_store_creates_prakerin(): void
    {
        $response = $this->postJson('/api/v1/tu/prakerin', [
            'nama_perusahaan' => 'PT Baru',
            'alamat' => 'Jl. Test',
            'PIC' => 'Pak Budi',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-06-30',
        ], $this->authHeaders());

        $response->assertCreated()
            ->assertJsonPath('data.nama_perusahaan', 'PT Baru');

        $this->assertDatabaseHas('prakerin', ['nama_perusahaan' => 'PT Baru']);
    }

    public function test_store_validates_required(): void
    {
        $response = $this->postJson('/api/v1/tu/prakerin', [], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['nama_perusahaan']);
    }

    public function test_update_modifies_prakerin(): void
    {
        $prakerin = Prakerin::create([
            'nama_perusahaan' => 'Old',
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);

        $response = $this->putJson('/api/v1/tu/prakerin/'.$prakerin->id, [
            'nama_perusahaan' => 'New',
        ], $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.nama_perusahaan', 'New');
    }

    public function test_destroy_deletes_prakerin(): void
    {
        $prakerin = Prakerin::create([
            'nama_perusahaan' => 'To Delete',
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);

        $response = $this->deleteJson('/api/v1/tu/prakerin/'.$prakerin->id, [], $this->authHeaders());

        $response->assertOk();
        $this->assertDatabaseMissing('prakerin', ['id' => $prakerin->id]);
    }

    public function test_store_peserta(): void
    {
        $prakerin = Prakerin::create([
            'nama_perusahaan' => 'PT Test',
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);
        $siswa = Siswa::factory()->create(['aktif' => 1]);

        $response = $this->postJson('/api/v1/tu/prakerin/peserta', [
            'prakerin_id' => $prakerin->id,
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
        ], $this->authHeaders());

        $response->assertCreated();
        $this->assertDatabaseHas('siswa_prakerin', [
            'prakerin_id' => $prakerin->id,
            'siswa_id' => $siswa->id,
        ]);
    }

    public function test_peserta_index(): void
    {
        $prakerin = Prakerin::create([
            'nama_perusahaan' => 'PT Test',
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);
        $siswa = Siswa::factory()->create(['aktif' => 1]);
        SiswaPrakerin::create([
            'prakerin_id' => $prakerin->id,
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->sem->id,
        ]);

        $response = $this->getJson('/api/v1/tu/prakerin/peserta?prakerin_id='.$prakerin->id, $this->authHeaders());

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_requires_tu_role(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);
        $token = $guru->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/tu/prakerin');

        $response->assertForbidden();
    }
}
