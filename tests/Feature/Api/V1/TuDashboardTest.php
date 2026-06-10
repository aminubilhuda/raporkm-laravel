<?php

namespace Tests\Feature\Api\V1;

use App\Models\Kelas;
use App\Models\KelompokMapel;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TuDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $tuUser;

    private TahunPelajaran $tp;

    private Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tp = TahunPelajaran::create(['tahun' => '2025/2026', 'status' => 1]);
        $this->semester = Semester::create(['nama' => 'Ganjil', 'urutan' => 1, 'status' => 1]);
        Sekolah::create([
            'npsn' => '12345678',
            'nama_sekolah' => 'SMK Abdi Negara Tuban',
            'tahun_aktif' => $this->tp->id,
            'semester_aktif' => $this->semester->id,
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

    public function test_dashboard_returns_success(): void
    {
        $response = $this->getJson('/api/v1/tu/dashboard', $this->authHeaders());

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'total_siswa',
                    'total_kelas',
                    'total_mapel',
                    'total_guru',
                    'siswa_laki',
                    'siswa_perempuan',
                    'tahun_aktif',
                    'semester_aktif',
                    'progress_semester',
                    'siswa_per_tingkat',
                    'mapel_per_kelompok',
                    'recent_activity',
                ],
            ]);
    }

    public function test_dashboard_counts_students(): void
    {
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semester->id,
        ]);

        $siswa1 = Siswa::factory()->create(['aktif' => 1, 'kelamin' => 1]);
        $siswa2 = Siswa::factory()->create(['aktif' => 1, 'kelamin' => 2]);
        SiswaKelas::create([
            'siswa_id' => $siswa1->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semester->id,
            'status' => 'aktif',
        ]);
        SiswaKelas::create([
            'siswa_id' => $siswa2->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semester->id,
            'status' => 'aktif',
        ]);

        $response = $this->getJson('/api/v1/tu/dashboard', $this->authHeaders());

        $response->assertOk();
        $data = $response->json('data');
        $this->assertSame(2, $data['total_siswa']);
        $this->assertSame(1, $data['siswa_laki']);
        $this->assertSame(1, $data['siswa_perempuan']);
    }

    public function test_dashboard_counts_mapel_and_kelas(): void
    {
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semester->id,
        ]);
        Mapel::create(['kelompok_mapel_id' => KelompokMapel::create(['nama' => 'A'])->id, 'kode' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75]);
        Mapel::create(['kelompok_mapel_id' => KelompokMapel::create(['nama' => 'B'])->id, 'kode' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia', 'kkm' => 75]);

        $response = $this->getJson('/api/v1/tu/dashboard', $this->authHeaders());

        $response->assertOk();
        $data = $response->json('data');
        $this->assertSame(1, $data['total_kelas']);
        $this->assertSame(2, $data['total_mapel']);
    }

    public function test_dashboard_requires_tu_role(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);
        $token = $guru->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/tu/dashboard');

        $response->assertForbidden();
    }
}
