<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\NilaiMapel;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaporPdfTest extends TestCase
{
    use RefreshDatabase;

    private User $tu;

    private TahunPelajaran $tahun;

    private Semester $semester;

    private Siswa $siswa;

    private Kelas $kelas;

    private Mapel $mapel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tu = User::factory()->tataUsaha()->create();

        $this->tahun = TahunPelajaran::factory()->create();
        $this->semester = Semester::factory()->create();
        Sekolah::factory()->create([
            'tahun_aktif' => $this->tahun->id,
            'semester_aktif' => $this->semester->id,
        ]);

        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();
        $this->kelas = Kelas::factory()->create([
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $jurusan->id,
        ]);
        $this->mapel = Mapel::factory()->create(['kkm' => 75]);
        $this->siswa = Siswa::factory()->create();

        SiswaKelas::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'status' => 'aktif',
        ]);
    }

    public function test_tu_dapat_melihat_halaman_pilih_rapor(): void
    {
        $this->actingAs($this->tu);

        $response = $this->get(route('tu.rapor.pilih'));

        $response->assertOk();
        $response->assertViewIs('tu.rapor.pilih');
        $response->assertViewHas('siswaList');
    }

    public function test_tu_dapat_download_rapor_semester_pdf(): void
    {
        NilaiMapel::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 85,
            'predikat' => 'B',
        ]);

        $this->actingAs($this->tu);

        $response = $this->get(route('tu.rapor.semester', [
            'siswa' => $this->siswa->id,
            'tahun' => $this->tahun->id,
            'semester' => $this->semester->id,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_guru_tidak_boleh_download_rapor_siswa(): void
    {
        $guru = User::factory()->guru()->create();
        $this->actingAs($guru);

        $response = $this->get(route('tu.rapor.semester', [
            'siswa' => $this->siswa->id,
            'tahun' => $this->tahun->id,
            'semester' => $this->semester->id,
        ]));

        $response->assertForbidden();
    }
}
