<?php

namespace Tests\Feature\Api\V1;

use App\Models\Kelas;
use App\Models\KelasWali;
use App\Models\KelompokMapel;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruKelasKuTest extends TestCase
{
    use RefreshDatabase;

    private User $guruUser;

    private Kelas $kelas;

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
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $this->kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
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

    public function test_index_returns_kelas_where_guru_teaches(): void
    {
        $tp = TahunPelajaran::first();
        $sem = Semester::first();
        $mapel = Mapel::create(['kelompok_mapel_id' => KelompokMapel::create(['nama' => 'A'])->id, 'kode' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75]);
        MapelKelas::create([
            'mapel_id' => $mapel->id,
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guruUser->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);

        $response = $this->getJson('/api/v1/guru/kelas-ku', $this->authHeaders());

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [['id', 'nama_kelas', 'tingkat', 'jurusan', 'jumlah_siswa', 'is_wali', 'mapel']],
            ]);
    }

    public function test_index_returns_wali_kelas(): void
    {
        $tp = TahunPelajaran::first();
        $sem = Semester::first();
        KelasWali::create([
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guruUser->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);

        $response = $this->getJson('/api/v1/guru/kelas-ku', $this->authHeaders());

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertTrue($data[0]['is_wali']);
    }

    public function test_siswa_returns_class_students(): void
    {
        $tp = TahunPelajaran::first();
        $sem = Semester::first();
        $siswa = Siswa::factory()->create(['aktif' => 1]);
        SiswaKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
            'status' => 'aktif',
        ]);

        $response = $this->getJson('/api/v1/guru/kelas-ku/'.$this->kelas->id.'/siswa', $this->authHeaders());

        $response->assertOk()
            ->assertJsonCount(1, 'data.siswa');
    }

    public function test_requires_guru_role(): void
    {
        $tu = User::factory()->create(['jabatan' => 2]);
        $token = $tu->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/guru/kelas-ku');

        $response->assertForbidden();
    }
}
