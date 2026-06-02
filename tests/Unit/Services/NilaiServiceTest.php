<?php

namespace Tests\Unit\Services;

use App\Models\DeskripsiRapor;
use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\NilaiFormatif;
use App\Models\NilaiMapel;
use App\Models\NilaiSumatifAs;
use App\Models\NilaiSumatifPh;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\TujuanPembelajaran;
use App\Services\NilaiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NilaiServiceTest extends TestCase
{
    use RefreshDatabase;

    private NilaiService $service;

    private TahunPelajaran $tahun;

    private Semester $semester;

    private Mapel $mapel;

    private Kelas $kelas;

    private Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new NilaiService;

        $this->tahun = TahunPelajaran::factory()->create();
        $this->semester = Semester::factory()->create();

        Sekolah::factory()->create([
            'tahun_aktif' => $this->tahun->id,
            'semester_aktif' => $this->semester->id,
        ]);

        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();

        $this->mapel = Mapel::factory()->create(['kkm' => 75]);
        $this->kelas = Kelas::factory()->create([
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $jurusan->id,
        ]);
        $this->siswa = Siswa::factory()->create();
    }

    public function test_hitung_nilai_akhir_menggunakan_bobot_40_30_30(): void
    {
        $tp1 = TujuanPembelajaran::factory()->create([
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
        $tp2 = TujuanPembelajaran::factory()->create([
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        NilaiFormatif::factory()->create([
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $tp1->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 80,
        ]);
        NilaiFormatif::factory()->create([
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $tp2->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 90,
        ]);

        NilaiSumatifPh::factory()->create([
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $tp1->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 70,
        ]);

        NilaiSumatifAs::factory()->create([
            'siswa_id' => $this->siswa->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 85,
        ]);

        $hasil = $this->service->hitungNilaiAkhirMapel(
            $this->siswa->id,
            $this->kelas->id,
            $this->mapel->id,
            $this->tahun->id,
            $this->semester->id,
        );

        $this->assertEquals(85.0, $hasil['rataFormatif']);
        $this->assertEquals(70.0, $hasil['rataPh']);
        $this->assertEquals(85, $hasil['sumatifAs']);
        $this->assertEquals(81, $hasil['nilaiAkhir']);
    }

    public function test_hitung_nilai_akhir_dengan_tanpa_nilai_adalah_nol(): void
    {
        $hasil = $this->service->hitungNilaiAkhirMapel(
            $this->siswa->id,
            $this->kelas->id,
            $this->mapel->id,
            $this->tahun->id,
            $this->semester->id,
        );

        $this->assertEquals(0, $hasil['nilaiAkhir']);
    }

    public function test_get_predikat_SB_untuk_nilai_90_ke_atas(): void
    {
        $this->assertEquals('SB', $this->service->getPredikat(90));
        $this->assertEquals('SB', $this->service->getPredikat(100));
    }

    public function test_get_predikat_B_untuk_nilai_75_ke_atas(): void
    {
        $this->assertEquals('B', $this->service->getPredikat(75));
        $this->assertEquals('B', $this->service->getPredikat(89));
    }

    public function test_get_predikat_C_untuk_nilai_60_ke_atas(): void
    {
        $this->assertEquals('C', $this->service->getPredikat(60));
        $this->assertEquals('C', $this->service->getPredikat(74));
    }

    public function test_get_predikat_PB_dibawah_60(): void
    {
        $this->assertEquals('PB', $this->service->getPredikat(59));
        $this->assertEquals('PB', $this->service->getPredikat(0));
    }

    public function test_generate_deskripsi_mengambil_template_SB(): void
    {
        DeskripsiRapor::factory()->predikat('SB')->create([
            'deskripsi' => 'Sangat Baik dalam menguasai materi.',
        ]);

        $hasil = $this->service->generateDeskripsi(95, 75);

        $this->assertStringContainsString('Sangat Baik', $hasil);
    }

    public function test_generate_deskripsi_mengambil_template_PB(): void
    {
        DeskripsiRapor::factory()->predikat('PB')->create([
            'deskripsi' => 'Perlu bimbingan dalam materi.',
        ]);

        $hasil = $this->service->generateDeskripsi(50, 75);

        $this->assertStringContainsString('Perlu bimbingan', $hasil);
    }

    public function test_generate_deskripsi_default_jika_template_tidak_ada(): void
    {
        $hasil = $this->service->generateDeskripsi(95, 75);

        $this->assertEquals('-', $hasil);
    }

    public function test_simpan_nilai_akhir_upsert_ke_tabel_nilai_mapel(): void
    {
        $tp = TujuanPembelajaran::factory()->create([
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        NilaiFormatif::factory()->create([
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $tp->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 80,
        ]);
        NilaiSumatifPh::factory()->create([
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $tp->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 80,
        ]);
        NilaiSumatifAs::factory()->create([
            'siswa_id' => $this->siswa->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 80,
        ]);

        DeskripsiRapor::factory()->predikat('B')->create();

        $nilai = $this->service->simpanNilaiAkhir(
            $this->siswa->id,
            $this->kelas->id,
            $this->mapel->id,
            $this->tahun->id,
            $this->semester->id,
        );

        $this->assertInstanceOf(NilaiMapel::class, $nilai);
        $this->assertEquals(80, $nilai->nilai);
        $this->assertEquals('B', $nilai->predikat);
        $this->assertEquals(75, $nilai->kktp);

        $this->assertDatabaseHas('nilai_mapel', [
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 80,
            'predikat' => 'B',
        ]);
    }

    public function test_simpan_nilai_akhir_update_bukan_insert_jika_sudah_ada(): void
    {
        NilaiMapel::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 50,
            'predikat' => 'PB',
        ]);

        $tp = TujuanPembelajaran::factory()->create([
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
        NilaiFormatif::factory()->create([
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $tp->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 90,
        ]);
        NilaiSumatifPh::factory()->create([
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $tp->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 90,
        ]);
        NilaiSumatifAs::factory()->create([
            'siswa_id' => $this->siswa->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 90,
        ]);
        DeskripsiRapor::factory()->predikat('SB')->create();

        $this->service->simpanNilaiAkhir(
            $this->siswa->id,
            $this->kelas->id,
            $this->mapel->id,
            $this->tahun->id,
            $this->semester->id,
        );

        $this->assertEquals(1, NilaiMapel::where('siswa_id', $this->siswa->id)->count());
        $this->assertEquals(90, NilaiMapel::where('siswa_id', $this->siswa->id)->value('nilai'));
        $this->assertEquals('SB', NilaiMapel::where('siswa_id', $this->siswa->id)->value('predikat'));
    }
}
