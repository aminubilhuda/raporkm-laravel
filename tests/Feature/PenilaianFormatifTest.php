<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\NilaiFormatif;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\TujuanPembelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenilaianFormatifTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private TahunPelajaran $tahun;

    private Semester $semester;

    private Mapel $mapel;

    private Kelas $kelas;

    private Siswa $siswa;

    private TujuanPembelajaran $tp1;

    private TujuanPembelajaran $tp2;

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

        $this->mapel = Mapel::factory()->create();
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
            'status' => 'aktif',
        ]);

        $this->tp1 = TujuanPembelajaran::factory()->create([
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
        $this->tp2 = TujuanPembelajaran::factory()->create([
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
    }

    public function test_formatif_menyimpan_nilai_per_tp(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.penilaian.formatif'), [
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'siswa_id' => [$this->siswa->id],
            'nilai' => [
                $this->tp1->id => 85,
                $this->tp2->id => 90,
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('nilai_formatif', [
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $this->tp1->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'nilai' => 85,
        ]);

        $this->assertDatabaseHas('nilai_formatif', [
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $this->tp2->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'nilai' => 90,
            'middle' => 88,
            'nas' => 89,
        ]);

        $this->assertEquals(2, NilaiFormatif::where('siswa_id', $this->siswa->id)->count());
    }

    public function test_formatif_update_nilai_yang_ada(): void
    {
        NilaiFormatif::factory()->create([
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $this->tp1->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'nilai' => 50,
        ]);

        $this->actingAs($this->guru)->post(route('guru.penilaian.formatif'), [
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'siswa_id' => [$this->siswa->id],
            'nilai' => [
                $this->tp1->id => 95,
            ],
        ])->assertRedirect();

        $this->assertEquals(1, NilaiFormatif::where('siswa_id', $this->siswa->id)->count());
        $this->assertDatabaseHas('nilai_formatif', [
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $this->tp1->id,
            'nilai' => 95,
        ]);
    }

    public function test_formatif_ditolak_jika_guru_bukan_pengajar_mapel(): void
    {
        $guruLain = User::factory()->guru()->create();

        $response = $this->actingAs($guruLain)->post(route('guru.penilaian.formatif'), [
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'siswa_id' => [$this->siswa->id],
            'nilai' => [
                $this->tp1->id => 85,
            ],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('nilai_formatif', 0);
    }
}
