<?php

namespace Tests\Feature\Api\V1;

use App\Models\Kelas;
use App\Models\KelompokMapel;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\TujuanPembelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruPenilaianTest extends TestCase
{
    use RefreshDatabase;

    private User $guruUser;

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

        $this->guruUser = User::factory()->create(['jabatan' => 3]);
    }

    private function authHeaders(): array
    {
        $token = $this->guruUser->createToken('test-device')->plainTextToken;

        return [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ];
    }

    public function test_index_requires_kelas_and_mapel(): void
    {
        $response = $this->getJson('/api/v1/guru/penilaian', $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['kelas_id', 'mapel_id']);
    }

    public function test_index_returns_penilaian_data(): void
    {
        $tp = TahunPelajaran::first();
        $sem = Semester::first();
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);
        $mapel = Mapel::create([
            'kelompok_mapel_id' => KelompokMapel::create(['nama' => 'A'])->id,
            'kode' => 'MTK',
            'nama_mapel' => 'Matematika',
            'kkm' => 75,
        ]);

        $response = $this->getJson('/api/v1/guru/penilaian?kelas_id='.$kelas->id.'&mapel_id='.$mapel->id, $this->authHeaders());

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['tp_list', 'nilai'],
            ]);
    }

    public function test_store_formatif(): void
    {
        $tp = TahunPelajaran::first();
        $sem = Semester::first();
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);
        $mapel = Mapel::create([
            'kelompok_mapel_id' => KelompokMapel::create(['nama' => 'A'])->id,
            'kode' => 'MTK',
            'nama_mapel' => 'Matematika',
            'kkm' => 75,
        ]);
        $tpModel = TujuanPembelajaran::create([
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'kode_tp' => 'TP.1',
            'nama_tp' => 'Memahami bilangan',
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);
        $siswa = Siswa::factory()->create(['aktif' => 1]);

        $response = $this->postJson('/api/v1/guru/penilaian/formatif', [
            'siswa_id' => $siswa->id,
            'tujuan_pembelajaran_id' => $tpModel->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'nilai' => 85,
        ], $this->authHeaders());

        $response->assertCreated()
            ->assertJsonPath('data.nilai', 85);

        $this->assertDatabaseHas('nilai_formatif', [
            'siswa_id' => $siswa->id,
            'tujuan_pembelajaran_id' => $tpModel->id,
            'nilai' => 85,
        ]);
    }

    public function test_store_formatif_validates_nilai(): void
    {
        $response = $this->postJson('/api/v1/guru/penilaian/formatif', [
            'siswa_id' => 1,
            'tujuan_pembelajaran_id' => 1,
            'kelas_id' => 1,
            'mapel_id' => 1,
            'nilai' => 150,
        ], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['nilai']);
    }

    public function test_requires_guru_role(): void
    {
        $tu = User::factory()->create(['jabatan' => 2]);
        $token = $tu->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/guru/penilaian?kelas_id=1&mapel_id=1');

        $response->assertForbidden();
    }
}
