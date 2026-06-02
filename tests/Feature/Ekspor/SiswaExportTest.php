<?php

namespace Tests\Feature\Ekspor;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaExportTest extends TestCase
{
    use RefreshDatabase;

    private User $tu;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tu = User::factory()->tataUsaha()->create();
    }

    public function test_tu_can_export_all_siswa(): void
    {
        Siswa::factory()->count(3)->create();

        $response = $this->actingAs($this->tu)->get(route('tu.ekspor.siswa'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_tu_can_export_siswa_filtered_by_kelas(): void
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

        $response = $this->actingAs($this->tu)->get(route('tu.ekspor.siswa', [
            'kelas_id' => $kelas->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="Siswa-Kelas-'.$kelas->id.'.xlsx"');
    }

    public function test_unauthenticated_user_cannot_export_siswa(): void
    {
        $response = $this->get(route('tu.ekspor.siswa'));
        $response->assertRedirect();
    }
}
