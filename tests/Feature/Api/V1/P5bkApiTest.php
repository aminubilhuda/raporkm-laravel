<?php

namespace Tests\Feature\Api\V1;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\ProyekKelas;
use App\Models\ProyekTema;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P5bkApiTest extends TestCase
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

    public function test_index_returns_tema_list(): void
    {
        $sekolah = Sekolah::first();
        ProyekTema::create([
            'nama_tema' => 'Lingkungan Bersih',
            'tahun_pelajaran_id' => $sekolah->tahun_aktif,
            'semester_id' => $sekolah->semester_aktif,
        ]);

        $response = $this->getJson('/api/v1/tu/p5bk', $this->authHeaders());

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [['id', 'nama_tema', 'keterangan', 'jumlah_kelas']],
            ]);
    }

    public function test_index_search(): void
    {
        $sekolah = Sekolah::first();
        ProyekTema::create([
            'nama_tema' => 'Lingkungan Bersih',
            'tahun_pelajaran_id' => $sekolah->tahun_aktif,
            'semester_id' => $sekolah->semester_aktif,
        ]);
        ProyekTema::create([
            'nama_tema' => 'Gotong Royong',
            'tahun_pelajaran_id' => $sekolah->tahun_aktif,
            'semester_id' => $sekolah->semester_aktif,
        ]);

        $response = $this->getJson('/api/v1/tu/p5bk?search=Lingkungan', $this->authHeaders());

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_store_creates_tema(): void
    {
        $response = $this->postJson('/api/v1/tu/p5bk', [
            'nama_tema' => 'Kebinekaan',
        ], $this->authHeaders());

        $response->assertCreated()
            ->assertJsonPath('data.nama_tema', 'Kebinekaan');

        $this->assertDatabaseHas('proyek_tema', ['nama_tema' => 'Kebinekaan']);
    }

    public function test_store_validates_required(): void
    {
        $response = $this->postJson('/api/v1/tu/p5bk', [], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['nama_tema']);
    }

    public function test_update_modifies_tema(): void
    {
        $sekolah = Sekolah::first();
        $tema = ProyekTema::create([
            'nama_tema' => 'Old',
            'tahun_pelajaran_id' => $sekolah->tahun_aktif,
            'semester_id' => $sekolah->semester_aktif,
        ]);

        $response = $this->putJson('/api/v1/tu/p5bk/'.$tema->id, [
            'nama_tema' => 'New',
        ], $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data.nama_tema', 'New');
    }

    public function test_destroy_deletes_tema(): void
    {
        $sekolah = Sekolah::first();
        $tema = ProyekTema::create([
            'nama_tema' => 'To Delete',
            'tahun_pelajaran_id' => $sekolah->tahun_aktif,
            'semester_id' => $sekolah->semester_aktif,
        ]);

        $response = $this->deleteJson('/api/v1/tu/p5bk/'.$tema->id, [], $this->authHeaders());

        $response->assertOk();
        $this->assertSoftDeleted('proyek_tema', ['id' => $tema->id]);
    }

    public function test_proyek_kelas_index(): void
    {
        $sekolah = Sekolah::first();
        $tp = TahunPelajaran::find($sekolah->tahun_aktif);
        $sem = Semester::find($sekolah->semester_aktif);
        $tema = ProyekTema::create([
            'nama_tema' => 'Lingkungan',
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);
        ProyekKelas::create([
            'proyek_tema_id' => $tema->id,
            'kelas_id' => $kelas->id,
            'judul' => 'Proyek Lingkungan',
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);

        $response = $this->getJson('/api/v1/tu/p5bk/proyek-kelas?proyek_tema_id='.$tema->id, $this->authHeaders());

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
        ])->getJson('/api/v1/tu/p5bk');

        $response->assertForbidden();
    }
}
