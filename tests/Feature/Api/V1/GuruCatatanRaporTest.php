<?php

namespace Tests\Feature\Api\V1;

use App\Models\CatatanWali;
use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruCatatanRaporTest extends TestCase
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

    public function test_index_returns_catatan(): void
    {
        $siswa = Siswa::factory()->create(['aktif' => 1]);
        $tp = TahunPelajaran::first();
        $sem = Semester::first();
        CatatanWali::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guruUser->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
            'catatan' => 'Siswa berprestasi',
        ]);

        $response = $this->getJson('/api/v1/guru/catatan-rapor?kelas_id='.$this->kelas->id, $this->authHeaders());

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_store_creates_catatan(): void
    {
        $siswa = Siswa::factory()->create(['aktif' => 1]);

        $response = $this->postJson('/api/v1/guru/catatan-rapor', [
            'siswa_id' => $siswa->id,
            'kelas_id' => $this->kelas->id,
            'catatan' => 'Perlu peningkatan',
        ], $this->authHeaders());

        $response->assertCreated();
        $this->assertDatabaseHas('catatan_wali', [
            'siswa_id' => $siswa->id,
            'catatan' => 'Perlu peningkatan',
        ]);
    }

    public function test_requires_guru_role(): void
    {
        $tu = User::factory()->create(['jabatan' => 2]);
        $token = $tu->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/guru/catatan-rapor?kelas_id=1');

        $response->assertForbidden();
    }
}
