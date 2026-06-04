<?php

namespace Tests\Unit\Services;

use App\Models\CatatanWali;
use App\Models\Eskul;
use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\NilaiMapel;
use App\Models\NilaiPrakerin;
use App\Models\NilaiSumatifTs;
use App\Models\Prakerin;
use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaEskul;
use App\Models\SiswaKelas;
use App\Models\SiswaPrakerin;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use App\Services\RaporService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaporServiceTest extends TestCase
{
    use RefreshDatabase;

    private RaporService $service;

    private TahunPelajaran $tahun;

    private Semester $semester;

    private Sekolah $sekolah;

    private Kelas $kelas;

    private Siswa $siswa;

    private Mapel $mapel;

    private User $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRefData();

        $this->service = new RaporService;

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
        $this->siswa = Siswa::factory()->create();
        $this->guru = User::factory()->guru()->create();

        SiswaKelas::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'status' => 'aktif',
        ]);
    }

    private function seedRefData(): void
    {
        \DB::table('jenis_absen')->insert([
            ['id' => 1, 'nama' => 'Hadir'],
            ['id' => 2, 'nama' => 'Sakit'],
            ['id' => 3, 'nama' => 'Izin'],
            ['id' => 4, 'nama' => 'Alpa'],
        ]);
    }

    public function test_get_data_rapor_semester_returns_complete_payload(): void
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
        CatatanWali::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $data = $this->service->getDataRaporSemester(
            $this->siswa->id,
            $this->tahun->id,
            $this->semester->id
        );

        $this->assertSame($this->siswa->id, $data['siswa']->id);
        $this->assertSame($this->sekolah->id, $data['sekolah']->id);
        $this->assertSame($this->kelas->id, $data['kelas']->id);
        $this->assertCount(1, $data['nilai_mapel']);
        $this->assertCount(1, $data['catatan_wali']);
        $this->assertArrayHasKey('ekskul', $data);
        $this->assertArrayHasKey('presensi', $data);
        $this->assertArrayHasKey('pkl', $data);
        $this->assertArrayHasKey('tahun', $data);
        $this->assertArrayHasKey('semester', $data);
    }

    public function test_data_rapor_siswa_tanpa_nilai_tetap_berhasil(): void
    {
        $data = $this->service->getDataRaporSemester(
            $this->siswa->id,
            $this->tahun->id,
            $this->semester->id
        );

        $this->assertSame($this->siswa->id, $data['siswa']->id);
        $this->assertCount(0, $data['nilai_mapel']);
        $this->assertCount(0, $data['catatan_wali']);
    }

    public function test_data_rapor_mid_berisi_sumatif_ts_saja(): void
    {
        NilaiSumatifTs::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 80,
        ]);
        NilaiMapel::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 85,
        ]);

        $data = $this->service->getDataRaporMid(
            $this->siswa->id,
            $this->tahun->id,
            $this->semester->id
        );

        $this->assertCount(1, $data['nilai_sumatif_ts']);
        $this->assertSame(80, $data['nilai_sumatif_ts']->first()->nilai);
    }

    public function test_data_rapor_ekskul_dan_presensi_ter_agregasi(): void
    {
        $eskul = Eskul::factory()->create(['sekolah_id' => $this->sekolah->id]);
        SiswaEskul::factory()->create([
            'siswa_id' => $this->siswa->id,
            'eskul_id' => $eskul->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'predikat' => 'B',
        ]);
        Presensi::create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'jenis_absen_id' => 2,
            'tanggal' => '2026-03-01',
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
        Presensi::create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'jenis_absen_id' => 3,
            'tanggal' => '2026-03-02',
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
        Presensi::create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'jenis_absen_id' => 4,
            'tanggal' => '2026-03-03',
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $data = $this->service->getDataRaporSemester(
            $this->siswa->id,
            $this->tahun->id,
            $this->semester->id
        );

        $this->assertCount(1, $data['ekskul']);
        $this->assertSame(3, $data['presensi']['total']);
        $this->assertSame(1, $data['presensi']['sakit']);
        $this->assertSame(1, $data['presensi']['izin']);
        $this->assertSame(1, $data['presensi']['alpha']);
    }

    public function test_data_lager_nilai_grid_per_siswa_per_mapel(): void
    {
        $siswa2 = Siswa::factory()->create();
        SiswaKelas::factory()->create([
            'siswa_id' => $siswa2->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
        $mapel2 = Mapel::factory()->create(['kkm' => 75]);

        NilaiMapel::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 85,
        ]);
        NilaiMapel::factory()->create([
            'siswa_id' => $siswa2->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 90,
        ]);
        NilaiMapel::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $mapel2->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 75,
        ]);

        $data = $this->service->getDataLagerNilai(
            $this->kelas->id,
            $this->mapel->id,
            $this->tahun->id,
            $this->semester->id
        );

        $this->assertCount(2, $data['grid']);
        $this->assertSame(85, $data['grid'][$this->siswa->id][$this->mapel->id]->nilai);
        $this->assertSame(90, $data['grid'][$siswa2->id][$this->mapel->id]->nilai);
    }

    public function test_data_rapor_pkl_mengandung_siswa_prakerin(): void
    {
        $prakerin = Prakerin::factory()->create([
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
        $siswaPrakerin = SiswaPrakerin::factory()->create([
            'siswa_id' => $this->siswa->id,
            'prakerin_id' => $prakerin->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
        NilaiPrakerin::factory()->create([
            'siswa_prakerin_id' => $siswaPrakerin->id,
            'mapel_id' => $this->mapel->id,
            'nilai' => 85,
        ]);

        $data = $this->service->getDataRaporPkl($siswaPrakerin->id);

        $this->assertSame($siswaPrakerin->id, $data['siswa_prakerin']->id);
        $this->assertCount(1, $data['nilai_prakerin']);
        $this->assertSame(85, $data['nilai_prakerin']->first()->nilai);
    }
}
