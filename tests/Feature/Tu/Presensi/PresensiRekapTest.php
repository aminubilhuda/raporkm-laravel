<?php

namespace Tests\Feature\Tu\Presensi;

use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresensiRekapTest extends TestCase
{
    use RefreshDatabase;

    private User $tu;

    private User $guru;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRefData();
        $this->tu = User::factory()->tataUsaha()->create();
        $this->guru = User::factory()->guru()->create();
    }

    public function test_tu_can_view_presensi_rekap(): void
    {
        $this->actingAs($this->tu)
            ->get(route('tu.presensi.rekap'))
            ->assertStatus(200)
            ->assertSee('Presensi');
    }

    public function test_guru_cannot_access_presensi_rekap(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.presensi.rekap'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
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
