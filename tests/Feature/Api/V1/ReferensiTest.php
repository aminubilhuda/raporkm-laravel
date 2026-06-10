<?php

namespace Tests\Feature\Api\V1;

use App\Models\Dimensi;
use App\Models\KelompokMapel;
use App\Models\Mapel;
use App\Models\RefAgama;
use App\Models\RefBulan;
use App\Models\RefHari;
use App\Models\RefJenisKelamin;
use App\Models\RefKurikulum;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferensiTest extends TestCase
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

    public function test_referensi_index_returns_list_of_slugs(): void
    {
        $response = $this->getJson('/api/v1/referensi', $this->authHeaders());

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data',
            ]);

        $slugs = $response->json('data');
        $this->assertContains('agama', $slugs);
        $this->assertContains('jenis-kelamin', $slugs);
        $this->assertContains('mapel', $slugs);
    }

    public function test_referensi_agama(): void
    {
        RefAgama::create(['nama' => 'Islam']);
        RefAgama::create(['nama' => 'Kristen']);

        $response = $this->getJson('/api/v1/referensi/agama', $this->authHeaders());

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');

        $response->assertJsonPath('data.0.nama', 'Islam');
    }

    public function test_referensi_jenis_kelamin(): void
    {
        RefJenisKelamin::create(['nama' => 'Laki-laki']);
        RefJenisKelamin::create(['nama' => 'Perempuan']);

        $response = $this->getJson('/api/v1/referensi/jenis-kelamin', $this->authHeaders());

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_referensi_ordered_by_angka(): void
    {
        Tingkat::create(['nama' => 'XII', 'angka' => 3, 'fase' => 'F', 'urutan' => 3]);
        Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        Tingkat::create(['nama' => 'XI', 'angka' => 2, 'fase' => 'E', 'urutan' => 2]);

        $response = $this->getJson('/api/v1/referensi/tingkat', $this->authHeaders());

        $response->assertOk()->assertJsonCount(3, 'data');
        $data = $response->json('data');
        $this->assertSame('X', $data[0]['nama']);
        $this->assertSame('XI', $data[1]['nama']);
        $this->assertSame('XII', $data[2]['nama']);
    }

    public function test_referensi_tahun_pelajaran_descending(): void
    {
        TahunPelajaran::create(['tahun' => '2024/2025', 'status' => 0]);
        TahunPelajaran::create(['tahun' => '2023/2024', 'status' => 0]);

        $response = $this->getJson('/api/v1/referensi/tahun-pelajaran', $this->authHeaders());

        $response->assertOk()->assertJsonCount(3, 'data');
        $data = $response->json('data');
        $this->assertSame('2025/2026', $data[0]['tahun']);
        $this->assertSame('2024/2025', $data[1]['tahun']);
    }

    public function test_referensi_with_keterangan(): void
    {
        RefKurikulum::create(['nama' => 'Kurikulum Merdeka', 'keterangan' => 'K-13']);

        $response = $this->getJson('/api/v1/referensi/kurikulum', $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.0.nama', 'Kurikulum Merdeka')
            ->assertJsonPath('data.0.keterangan', 'K-13');
    }

    public function test_referensi_unknown_slug_returns_404(): void
    {
        $response = $this->getJson('/api/v1/referensi/nonexistent', $this->authHeaders());

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_referensi_requires_auth(): void
    {
        $response = $this->getJson('/api/v1/referensi/agama');

        $response->assertUnauthorized();
    }

    public function test_referensi_mapel_with_ordering(): void
    {
        $kk = KelompokMapel::create(['nama' => 'A']);

        Mapel::create([
            'kelompok_mapel_id' => $kk->id,
            'kode' => 'MTK',
            'nama_mapel' => 'Matematika',
            'kkm' => 75,
            'urutan' => 2,
        ]);
        Mapel::create([
            'kelompok_mapel_id' => $kk->id,
            'kode' => 'BIN',
            'nama_mapel' => 'Bahasa Indonesia',
            'kkm' => 75,
            'urutan' => 1,
        ]);

        $response = $this->getJson('/api/v1/referensi/mapel', $this->authHeaders());

        $response->assertOk()->assertJsonCount(2, 'data');
        $data = $response->json('data');
        $this->assertSame('Bahasa Indonesia', $data[0]['nama']);
        $this->assertSame('Matematika', $data[1]['nama']);
    }

    public function test_dimensi_elemen_combined_endpoint(): void
    {
        $dimensi = Dimensi::create([
            'nama' => 'Beriman',
            'keterangan' => 'Profil Pelajar Pancasila',
            'urutan' => 1,
        ]);
        $dimensi->elemens()->create([
            'nama' => 'Elemen 1',
            'keterangan' => 'Deskripsi',
            'urutan' => 1,
        ]);

        $response = $this->getJson('/api/v1/referensi/dimensi-elemen', $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.0.nama', 'Beriman')
            ->assertJsonCount(1, 'data.0.elemens');
    }

    public function test_referensi_bulan(): void
    {
        RefBulan::create(['nama' => 'Januari', 'urutan' => 1]);
        RefBulan::create(['nama' => 'Februari', 'urutan' => 2]);

        $response = $this->getJson('/api/v1/referensi/bulan', $this->authHeaders());

        $response->assertOk()->assertJsonCount(2, 'data');
        $data = $response->json('data');
        $this->assertSame('Januari', $data[0]['nama']);
    }

    public function test_referensi_hari(): void
    {
        RefHari::create(['nama' => 'Senin', 'urutan' => 1]);
        RefHari::create(['nama' => 'Selasa', 'urutan' => 2]);

        $response = $this->getJson('/api/v1/referensi/hari', $this->authHeaders());

        $response->assertOk()->assertJsonCount(2, 'data');
    }
}
