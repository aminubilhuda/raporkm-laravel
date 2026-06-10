<?php

namespace Tests\Feature\Api\V1;

use App\Models\Kelas;
use App\Models\KelasWali;
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

class GuruCetakRaporTest extends TestCase
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

    public function test_index_returns_empty_when_not_wali(): void
    {
        $response = $this->getJson('/api/v1/guru/cetak-rapor', $this->authHeaders());

        $response->assertOk()
            ->assertJsonPath('data', []);
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

        $response = $this->getJson('/api/v1/guru/cetak-rapor', $this->authHeaders());

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_siswa_returns_students_in_class(): void
    {
        $tp = TahunPelajaran::first();
        $sem = Semester::first();
        KelasWali::create([
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guruUser->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);
        $siswa = Siswa::factory()->create(['aktif' => 1]);
        SiswaKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
            'status' => 'aktif',
        ]);

        $response = $this->getJson('/api/v1/guru/cetak-rapor/'.$this->kelas->id.'/siswa', $this->authHeaders());

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_siswa_returns_403_when_not_wali(): void
    {
        $response = $this->getJson('/api/v1/guru/cetak-rapor/'.$this->kelas->id.'/siswa', $this->authHeaders());

        $response->assertForbidden();
    }

    public function test_cetak_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/guru/cetak-rapor', [], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['siswa_id', 'jenis', 'kelas_id']);
    }

    public function test_cetak_validates_jenis_values(): void
    {
        $response = $this->postJson('/api/v1/guru/cetak-rapor', [
            'siswa_id' => [1],
            'jenis' => 'invalid',
            'kelas_id' => 1,
        ], $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['jenis']);
    }

    public function test_cetak_returns_403_when_not_wali(): void
    {
        $siswa = Siswa::factory()->create(['aktif' => 1]);

        $response = $this->postJson('/api/v1/guru/cetak-rapor', [
            'siswa_id' => [$siswa->id],
            'jenis' => 'semester',
            'kelas_id' => $this->kelas->id,
        ], $this->authHeaders());

        $response->assertForbidden();
    }

    public function test_requires_guru_role(): void
    {
        $tu = User::factory()->create(['jabatan' => 2]);
        $token = $tu->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/guru/cetak-rapor');

        $response->assertForbidden();
    }
}
