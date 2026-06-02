<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\NilaiFormatif;
use App\Models\NilaiMapel;
use App\Models\NilaiSumatifAs;
use App\Models\NilaiSumatifPh;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\TujuanPembelajaran;
use App\Models\User;
use App\Services\NilaiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NilaiSumatifAsObserverTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private TahunPelajaran $tahun;

    private Semester $semester;

    private Mapel $mapel;

    private Kelas $kelas;

    private Siswa $siswa;

    private TujuanPembelajaran $tp;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->guru = User::factory()->guru()->create();

        MapelKelas::factory()->create([
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'kkm' => 75,
        ]);

        $this->siswa = Siswa::factory()->create();
        SiswaKelas::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $this->tp = TujuanPembelajaran::factory()->create([
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        NilaiFormatif::factory()->create([
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $this->tp->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 80,
        ]);
        NilaiSumatifPh::factory()->create([
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $this->tp->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 80,
        ]);
    }

    public function test_menyimpan_sumatif_as_otomatis_buat_record_nilai_mapel(): void
    {
        $this->assertDatabaseCount('nilai_mapel', 0);

        NilaiSumatifAs::create([
            'siswa_id' => $this->siswa->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 90,
        ]);

        $this->assertDatabaseCount('nilai_mapel', 1);
        $this->assertDatabaseHas('nilai_mapel', [
            'siswa_id' => $this->siswa->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
    }

    public function test_predikat_terupdate_saat_nilai_berubah(): void
    {
        $nilaiAs = NilaiSumatifAs::create([
            'siswa_id' => $this->siswa->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 50,
        ]);

        $first = NilaiMapel::first();
        $this->assertNotNull($first);
        $this->assertEquals(71, $first->nilai);

        $nilaiAs->update(['nilai' => 95]);

        $first->refresh();
        $this->assertEquals(85, $first->nilai);
        $this->assertEquals('B', $first->predikat);
    }

    public function test_nilai_mapel_tidak_terbuat_tanpa_sumatif_as(): void
    {
        $this->assertDatabaseCount('nilai_mapel', 0);

        app(NilaiService::class)->simpanNilaiAkhir(
            $this->siswa->id,
            $this->kelas->id,
            $this->mapel->id,
            $this->tahun->id,
            $this->semester->id,
        );

        $this->assertDatabaseCount('nilai_mapel', 1);
    }
}
