<?php

namespace Tests\Feature\Ekspor;

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

class PresensiExportTest extends TestCase
{
    use RefreshDatabase;

    private User $tu;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRefData();
        $this->tu = User::factory()->tataUsaha()->create();
    }

    public function test_tu_can_export_presensi_to_xlsx(): void
    {
        $tahun = TahunPelajaran::factory()->create();
        $semester = Semester::factory()->create();
        Sekolah::factory()->create(['tahun_aktif' => $tahun->id, 'semester_aktif' => $semester->id]);
        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();
        $kelas = Kelas::factory()->create(['tingkat_id' => $tingkat->id, 'kompetensi_keahlian_id' => $jurusan->id]);
        $siswa = Siswa::factory()->create();
        SiswaKelas::factory()->create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id,
            'semester_id' => $semester->id,
            'status' => 'aktif',
        ]);
        Presensi::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id,
            'semester_id' => $semester->id,
            'jenis_absen_id' => 2,
            'tanggal' => '2026-01-15',
        ]);

        $response = $this->actingAs($this->tu)->get(route('tu.ekspor.presensi', [
            'kelas_id' => $kelas->id,
            'tahun' => $tahun->id,
            'semester' => $semester->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_export_presensi_validates_kelas_id(): void
    {
        $response = $this->actingAs($this->tu)->get(route('tu.ekspor.presensi', [
            'kelas_id' => 99999,
            'tahun' => 1,
            'semester' => 1,
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
