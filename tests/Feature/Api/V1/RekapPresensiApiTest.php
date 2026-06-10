<?php

namespace Tests\Feature\Api\V1;

use App\Models\JenisAbsen;
use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapPresensiApiTest extends TestCase
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

    public function test_index_returns_rekap(): void
    {
        $sekolah = Sekolah::first();
        $tp = TahunPelajaran::find($sekolah->tahun_aktif);
        $sem = Semester::find($sekolah->semester_aktif);
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);
        $siswa = Siswa::factory()->create(['aktif' => 1]);
        SiswaKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
            'status' => 'aktif',
        ]);
        $jenis = JenisAbsen::create(['nama' => 'Hadir']);
        Presensi::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'jenis_absen_id' => $jenis->id,
            'tanggal' => '2026-06-01',
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);

        $response = $this->getJson('/api/v1/tu/rekap-presensi?kelas_id='.$kelas->id, $this->authHeaders());

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'kelas_id',
                    'jenis_absen' => [['id', 'nama', 'keterangan']],
                    'rekap' => [['siswa_id', 'nama_siswa', 'nisn', 'absensi']],
                ],
            ]);
    }

    public function test_index_requires_kelas_id(): void
    {
        $response = $this->getJson('/api/v1/tu/rekap-presensi', $this->authHeaders());

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['kelas_id']);
    }

    public function test_detail_returns_presensi(): void
    {
        $sekolah = Sekolah::first();
        $tp = TahunPelajaran::find($sekolah->tahun_aktif);
        $sem = Semester::find($sekolah->semester_aktif);
        $tingkat = Tingkat::create(['nama' => 'X', 'angka' => 1, 'fase' => 'D', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $kelas = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);
        $siswa = Siswa::factory()->create(['aktif' => 1]);
        $jenis = JenisAbsen::create(['nama' => 'Hadir']);
        Presensi::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'jenis_absen_id' => $jenis->id,
            'tanggal' => '2026-06-01',
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $sem->id,
        ]);

        $response = $this->getJson('/api/v1/tu/rekap-presensi/detail?kelas_id='.$kelas->id.'&siswa_id='.$siswa->id, $this->authHeaders());

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'tanggal', 'jenis', 'keterangan']],
            ]);
    }

    public function test_requires_tu_role(): void
    {
        $guru = User::factory()->create(['jabatan' => 3]);
        $token = $guru->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/tu/rekap-presensi?kelas_id=1');

        $response->assertForbidden();
    }
}
