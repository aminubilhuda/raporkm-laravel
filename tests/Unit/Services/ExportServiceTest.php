<?php

namespace Tests\Unit\Services;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\NilaiMapel;
use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Services\ExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExportService $service;

    private TahunPelajaran $tahun;

    private Semester $semester;

    private Sekolah $sekolah;

    private Kelas $kelas;

    private Mapel $mapel;

    private Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRefData();

        $this->service = new ExportService;

        $this->tahun = TahunPelajaran::factory()->create();
        $this->semester = Semester::factory()->create();
        $this->sekolah = Sekolah::factory()->create([
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
        $this->siswa = Siswa::factory()->create(['nama_siswa' => 'Ahmad']);
    }

    public function test_export_nilai_returns_streamed_response_with_xlsx_content_type(): void
    {
        NilaiMapel::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 80,
            'predikat' => 'B',
        ]);

        $response = $this->service->exportNilai(
            $this->kelas->id,
            $this->mapel->id,
            $this->tahun->id,
            $this->semester->id
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    public function test_export_presensi_groups_by_siswa_and_jenis_absen(): void
    {
        Presensi::create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'jenis_absen_id' => 2,
            'tanggal' => '2026-01-15',
        ]);
        Presensi::create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'jenis_absen_id' => 3,
            'tanggal' => '2026-01-16',
        ]);
        Presensi::create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'jenis_absen_id' => 4,
            'tanggal' => '2026-01-17',
        ]);

        $response = $this->service->exportPresensi(
            $this->kelas->id,
            $this->tahun->id,
            $this->semester->id
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_export_siswa_returns_all_when_kelas_id_null(): void
    {
        Siswa::factory()->count(3)->create();

        $response = $this->service->exportSiswa();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Siswa-Semua', $response->headers->get('Content-Disposition'));
    }

    public function test_export_siswa_filters_by_kelas_when_provided(): void
    {
        SiswaKelas::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'status' => 'aktif',
        ]);
        Siswa::factory()->create();

        $response = $this->service->exportSiswa($this->kelas->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString("Siswa-Kelas-{$this->kelas->id}", $response->headers->get('Content-Disposition'));
    }

    private function seedRefData(): void
    {
        \DB::table('jenis_absen')->insert([
            ['id' => 1, 'nama' => 'Hadir'],
            ['id' => 2, 'nama' => 'Sakit'],
            ['id' => 3, 'nama' => 'Izin'],
            ['id' => 4, 'nama' => 'Tanpa Keterangan'],
        ]);
    }
}
