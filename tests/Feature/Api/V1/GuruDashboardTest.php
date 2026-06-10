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

class GuruDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $guruUser;

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

        $this->guruUser = User::factory()->create([
            'jabatan' => 3,
            'username' => 'guru01',
            'password' => bcrypt('password123'),
        ]);
    }

    private function authHeaders(): array
    {
        $token = $this->guruUser->createToken('test-device')->plainTextToken;

        return [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ];
    }

    public function test_dashboard_returns_success(): void
    {
        $response = $this->getJson('/api/v1/guru/dashboard', $this->authHeaders());

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'total_kelas_wali',
                    'total_mapel_diajar',
                    'total_siswa',
                    'siswa_wali',
                    'kelas_wali',
                    'kelas_yang_diajar',
                    'progress_nilai',
                    'presensi_hari_ini',
                    'catatan_pending',
                    'tahun_aktif',
                    'semester_aktif',
                ],
            ]);
    }

    public function test_dashboard_with_wali_kelas(): void
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

        KelasWali::create([
            'user_id' => $this->guruUser->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semester->id,
        ]);

        $siswa = Siswa::factory()->create(['aktif' => 1]);
        SiswaKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semester->id,
            'status' => 'aktif',
        ]);

        $response = $this->getJson('/api/v1/guru/dashboard', $this->authHeaders());

        $response->assertOk();
        $data = $response->json('data');
        $this->assertSame(1, $data['total_kelas_wali']);
        $this->assertSame(1, $data['siswa_wali']);
        $this->assertCount(1, $data['kelas_wali']);
    }

    public function test_dashboard_with_mapel_diajar(): void
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
        $kk = KelompokMapel::create(['nama' => 'A']);
        $mapel = Mapel::create([
            'kelompok_mapel_id' => $kk->id,
            'kode' => 'MTK',
            'nama_mapel' => 'Matematika',
            'kkm' => 75,
            'urutan' => 1,
        ]);

        MapelKelas::create([
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'user_id' => $this->guruUser->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semester->id,
        ]);

        $siswa = Siswa::factory()->create(['aktif' => 1]);
        SiswaKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semester->id,
            'status' => 'aktif',
        ]);

        $response = $this->getJson('/api/v1/guru/dashboard', $this->authHeaders());

        $response->assertOk();
        $data = $response->json('data');
        $this->assertSame(1, $data['total_mapel_diajar']);
        $this->assertNotEmpty($data['kelas_yang_diajar']);
        $this->assertSame('X TKJ 1', $data['kelas_yang_diajar'][0]['nama_kelas']);
        $this->assertSame(1, $data['kelas_yang_diajar'][0]['jumlah_siswa']);
    }

    public function test_dashboard_requires_guru_role(): void
    {
        $tu = User::factory()->create(['jabatan' => 2]);
        $token = $tu->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/guru/dashboard');

        $response->assertForbidden();
    }

    public function test_dashboard_with_empty_data(): void
    {
        $response = $this->getJson('/api/v1/guru/dashboard', $this->authHeaders());

        $response->assertOk();
        $data = $response->json('data');
        $this->assertSame(0, $data['total_kelas_wali']);
        $this->assertSame(0, $data['total_mapel_diajar']);
        $this->assertSame(0, $data['total_siswa']);
        $this->assertSame(0, $data['progress_nilai']);
        $this->assertSame(0, $data['catatan_pending']);
        $this->assertEmpty($data['kelas_wali']);
        $this->assertEmpty($data['kelas_yang_diajar']);
    }
}
