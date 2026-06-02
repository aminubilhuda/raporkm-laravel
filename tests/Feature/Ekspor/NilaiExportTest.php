<?php

namespace Tests\Feature\Ekspor;

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

class NilaiExportTest extends TestCase
{
    use RefreshDatabase;

    private User $tu;

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

        $this->tu = User::factory()->tataUsaha()->create();
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
        $this->mapel = Mapel::factory()->create();
        $this->siswa = Siswa::factory()->create();
        SiswaKelas::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'status' => 'aktif',
        ]);
        NilaiMapel::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
    }

    public function test_tu_can_export_nilai_to_xlsx(): void
    {
        $response = $this->actingAs($this->tu)->get(route('tu.ekspor.nilai', [
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'tahun' => $this->tahun->id,
            'semester' => $this->semester->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_export_nilai_requires_valid_inputs(): void
    {
        $response = $this->actingAs($this->tu)->get(route('tu.ekspor.nilai', [
            'kelas_id' => 99999,
            'mapel_id' => $this->mapel->id,
            'tahun' => $this->tahun->id,
            'semester' => $this->semester->id,
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('kelas_id');
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
